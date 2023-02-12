<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\WareResource;
use App\Models\Coin;
use App\Models\CoinLog;
use App\Models\OVip;
use App\Models\Silver;
use App\Models\SilverHestory;
use App\Models\User;
use App\Models\UserVip;
use App\Models\VipPrivilege;
use App\Models\Ware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->type){
            return Common::apiResponse (false,'type is required',null,422);
        }
        $wares = Ware::query ()
            ->where ('enable',1)
            ->whereIn ('get_type',[4,6])
            ->where ('type',$request->type)
            ->get ();
        return Common::apiResponse (true,'',WareResource::collection ($wares),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function silver_value(){
        $data = Silver::query ()->select ('id','coin','silver')->orderBy ('sort')->get ();
        return Common::apiResponse (1,'',$data,200);
    }

    public function buySilverCoins(Request $request){
        if (!$request->silver_id) return Common::apiResponse (0,'missing params',null,422);
        $user = $request->user ();
        $silver = Silver::query ()->find ($request->silver_id);
        if (!$silver) return Common::apiResponse (0,'not found',null,404);
        if ($user->di < $silver->coin) return Common::apiResponse (0,'low balance',null,405);
        DB::beginTransaction ();
        try {
            SilverHestory::query ()->create (
                [
                    'coins'=>$silver->coin,
                    'silvers'=>$silver->silver,
                    'silver_id'=>$silver->id,
                    'user_id'=>$user->id
                ]
            );
            $user->gold += $silver->silver;
            $user->di -= $silver->coin;
            $user->save();
            DB::commit ();
            return Common::apiResponse (1,'',new UserResource($user),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'failed',null,400);
        }
    }

    public function silver_history(Request $request){
        $user = $request->user ();
        $hes = SilverHestory::query ()->where ('user_id',$user->id)->select ('coins','silvers','created_at as time')->get ();
        return Common::apiResponse (1,'',$hes,200);
    }

    public function coinList(){
        $data = Coin::query ()->select ('id','usd','coin')->get ();
        return Common::apiResponse (1,'',$data,200);
    }

    public function buyCoins(Request $request){
        if(!$request->pay_method || !$request->coin_id) return Common::apiResponse (0,'missing param',null,422);
        $user = $request->user ();
        $coin = Coin::query ()->find ($request->coin_id);
        if(!$coin) return Common::apiResponse (0,'not found',null,404);
        DB::beginTransaction ();
        try {
            $log = CoinLog::query ()->create (
                [
                    'paid_usd'=>$coin->usd,
                    'obtained_coins'=>$coin->coin,
                    'user_id'=>$user->id,
                    'method'=>$request->pay_method,
                    'trx'=>'125487996663355888',
                    'status'=>0
                ]
            );
            if ($log->status == 1){
                $user->increment ('di',$log->obtained_coins);
            }
            DB::commit ();
            return Common::apiResponse (1,'done',null,201);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }
    }


    public function vipList(){
        $list = OVip::query ()->orderBy ('level')->get ()->map(function ($i){
            $privs = VipPrivilege::query ()->get ()->map(function ($p) use ($i){
                $mp = $i->privilegs()->pluck('id')->toArray();
                if (in_array ($p->id,$mp)){
                    $p->active = true;
                }else{
                    $p->active = false;
                }
                if ($p->getItem($i->level)){
                    $p->item = new WareResource($p->getItem($i->level));
                }else{
                    $p->item = new \stdClass();
                }

                return $p;
            });
            $i->privilegs = $privs;
            return $i;
        });
        return Common::apiResponse (1,'',$list,200);
    }

    public function buyVip(Request $request){
        if (!$request->vip_id ) return Common::apiResponse (0,'missing param',null,422);

        $vip = OVip::query ()->find ($request->vip_id);
        if (!$vip) return Common::apiResponse (0,'not found',null,404);
        $qty = $request->qty?:1;
        $total = $vip->price * $qty;
        $expire = $vip->expire;
        if ($expire == 0){
            $ex = 0;
        }else{
            $ex = now ()->addDays ($expire * $qty)->timestamp;
        }
        if ($request->type == 1){
            $type = 1;
            if (!$request->to_user) return Common::apiResponse (0,'missing param',null,422);
            $user_id = $request->to_user;
            $user = User::query ()->find ($user_id);
            if (!$user) return Common::apiResponse (0,'not found',null,404);
            $sender= $request->user ();
            $sender_id = $sender->id;
            if ($sender->di < $total) return Common::apiResponse (0,'balance low',null,407);
            $from = $sender;
        }else{
            $type = 0;
            $user = $request->user ();
            $user_id = $user->id;
            $sender_id = 0;
            if ($user->di < $total) return Common::apiResponse (0,'balance low',null,407);
            $from = $user;
        }

        DB::beginTransaction ();
        try {
            $from->decrement ('di',$total);
            $uvip = UserVip::query ()->create (
                [
                    'type'=>$type,
                    'sender_id'=>$sender_id,
                    'user_id'=>$user_id,
                    'vip_id'=>$vip->id,
                    'expire'=>$ex,
                    'qty'=>$qty,
                    'price'=>$vip->price,
                    'total'=>$total
                ]
            );
            $user->update (['vip'=>$uvip->id]);
            DB::commit ();
            return Common::apiResponse (1,'done',null,201);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }


    }


}
