<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\WareResource;
use App\Models\Silver;
use App\Models\SilverHestory;
use App\Models\User;
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

    public function buyVip(){

    }
}
