<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Agora;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Background;
use App\Models\Country;
use App\Models\Exchange;
use App\Models\ExchangeLog;
use App\Models\GiftLog;
use App\Models\LiveTime;
use App\Models\OVip;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vip;
use App\Models\VipPrivilege;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function allCountries(){
        $countries = Country::query ()->where ('status',1)->get ();
        return Common::apiResponse (1,'',CountryResource::collection ($countries));
    }
    public function getCountry($id){
        $country = Country::find($id);
        if ($country){
            return Common::apiResponse (1,'',new CountryResource($country));
        }
        return Common::apiResponse (0,__ ('not found'),null,404);
    }

    public function allClasses(){
        return Common::apiResponse (1,'',RoomCategory::query ()->whereDoesntHave ('parent')->select ('id','name','img')->get ());
    }

    public function getClassChildren($id){
        $class = RoomCategory::query ()->find ($id);
        if ($class){
            return Common::apiResponse (1,'',$class->children);
        }
        return Common::apiResponse (0,'not found',null,404);
    }

    public function getTypes(){
        return Common::apiResponse (1,'',RoomCategory::query ()->whereHas ('parent')->select ('id','name','img')->get ());
    }

    public function allBackgrounds(){
        return Common::apiResponse (1,'',Background::query ()->where('enable',1)->select ('id','img')->get (),200);
    }


    public function generateAgoraToken(Request $request){
        $user_id = $request->user ()->id;
        if ($request->type == 'rtc'){
            $token = Agora::RTCToken ($request->user_id?:$user_id,$request->channel_name,$request->role);
            if (!$token){
                return Common::apiResponse (0,'error');
            }
            return Common::apiResponse (1,'',['rtc_token'=>$token]);
        }
        $token = Agora::RTMToken ($request->user_id?:$user_id);
        if (!$token){
            return Common::apiResponse (0,'error');
        }
        return Common::apiResponse (1,'',['rtm_token'=>$token]);
    }


    public function one_page(Request $request){
        $type = $request->type;
        if(!$type)  return Common::apiResponse(0,'Missing parameters',null,422);
        $data=DB::table('pages')->where(['type'=>$type])->get();
        return Common::apiResponse(1,'',$data);
    }

    public function countVipsold(){
        $count = Vip::query ()->where ('type',3)->count ();
        $vips =  Vip::query ()->where ('type',3)->select ('id','level','type','img')->get ();
        return Common::apiResponse (1,'',['vip_count'=>$count,'vips'=>$vips]);
    }

    public function countVips(){
        $count = OVip::query ()->count ();
        $list = OVip::query ()->orderBy ('level')->get ()->map(function ($i){
            $privs = VipPrivilege::query ()->get ()->map(function ($p) use ($i){
                $mp = $i->privilegs()->pluck('id')->toArray();
                if (in_array ($p->id,$mp)){
                    $p->active = true;
                }else{
                    $p->active = false;
                }
                return $p;
            });
            $i->privilegs = $privs;
            return $i;
        });
        return Common::apiResponse (1,'',['vip_count'=>$count,'vips'=>$list]);
    }

    public function check_if_friend(Request $request){
        $me = $request->user ();
        $user_id = $request->user_id;
        if (in_array ($user_id,$me->friends_ids()->toArray())){
            return Common::apiResponse (1,'exists',true);
        }
        return Common::apiResponse (1,'does not exists',false);
    }


    public function getTimes(Request $request){
        $user_id = $request->user_id?:$request->user ()->id;
        $hours = 0;
        $days = 0;
        $diamonds = 0;
        $today = false;
        if ($request->time == 'today'){
            $times = LiveTime::query ()->where ('uid',$user_id)
                ->whereYear ('created_at','=',Carbon::now ()->year)
                ->whereMonth ('created_at','=',Carbon::now ()->month)
                ->whereDay ('created_at','=',Carbon::now ()->day)
                ->selectRaw('uid, SUM(hours) as hnum, SUM(days) as dnum')
                ->groupBy ('uid')
                ->first ()
            ;
            if ($times){
                $hours = $times->hnum;
                $days = $times->dnum;
            }

            $gifts_d = GiftLog::query ()->where ('receiver_id',$user_id)
                ->whereYear ('created_at','=',Carbon::now ()->year)
                ->whereMonth ('created_at','=',Carbon::now ()->month)
                ->whereDay ('created_at','=',Carbon::now ()->day)
                ->sum ('receiver_obtain');
            $diamonds = $gifts_d;
            if ($days >= 1){
                $today = true;
            }
        }elseif($request->time == 'month'){
            $times = LiveTime::query ()->where ('uid',$user_id)
                ->whereYear ('created_at','=',Carbon::now ()->year)
                ->whereMonth ('created_at','=',Carbon::now ()->month)
                ->selectRaw('uid, SUM(hours) as hnum, SUM(days) as dnum')
                ->groupBy ('uid')
                ->first ()
            ;
            if ($times){
                $hours = $times->hnum;
                $days = $times->dnum;
            }

            $gifts_d = GiftLog::query ()->where ('receiver_id',$user_id)
                ->whereYear ('created_at','=',Carbon::now ()->year)
                ->whereMonth ('created_at','=',Carbon::now ()->month)
                ->sum ('receiver_obtain');
            $diamonds = $gifts_d;
        }else{
            $times = LiveTime::query ()->where ('uid',$user_id)
                ->selectRaw('uid, SUM(hours) as hnum, SUM(days) as dnum')
                ->groupBy ('uid')
                ->first ()
            ;
            if ($times){
                $hours = $times->hnum;
                $days = $times->dnum;
            }

            $gifts_d = GiftLog::query ()->where ('receiver_id',$user_id)
                ->sum ('receiver_obtain');
            $diamonds = $gifts_d;
        }

        $hours = gmdate('H:i:s', $hours * 60 * 60);

        return Common::apiResponse (1,'',['diamonds'=>(integer)$diamonds,'days'=>$days,'hours'=>$hours, 'today'=>$today],200);

    }

    public function hidePk(Request $request){
        if (!$request->owner_id) return Common::apiResponse (0,'missing param',null,422);
        $user = $request->user ();
        $room = Room::query ()->where ('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        $d = [
            "messageContent"=>[
                "message"=>"hidePK",
            ]
        ];
        $json = json_encode ($d);
        Common::sendToZego ('SendCustomCommand',$room->id,$user->id,$json);
        return Common::apiResponse (1,'done',null,201);
    }


    public function openTicket(Request $request){
        if (!$request->contact || !$request->txt){
            return Common::apiResponse (0,'missing params');
        }

        $tkt = Ticket::query ()->create (
            [
                'user_id'=>$request->user_id,
                'contact_num'=>$request->contact,
                'problem'=>$request->txt,
                'status'=>1
            ]
        );
        if ($request->hasFile ('img')){
            $img = $request->file ('img');
            $path = Common::upload ('ticket',$img);
            $tkt->img = $path;
            $tkt->save ();
        }
        $out = [
            'contact'=>$tkt->contact_num,
            'txt'=>$tkt->problem,
            'image'=>$tkt->img
        ];
        return Common::apiResponse (1,'done',$out,200);
    }


    public function sendToZego(Request $request){
        $ms = [
            'messageContent'=>[
                'message'=>$request->message,
            ]
        ];
        $ex = json_decode ($request->ext,true);
        if (is_array($ex)){
            foreach ($ex as $k=>$value){
                $ms['messageContent'][$k] = $value;
            }
        }

        $json = json_encode ($ms);
        $user_id = $request->user_id?:0;
        $action = $request->action?:'SendCustomCommand';
        $room = Room::query ()->where('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        Common::sendToZego ($action,$room->id,$user_id,$json);
        return Common::apiResponse (1,'done',null,201);
    }

    public function exchangeList(Request $request){
        $user = $request->user ();
        if ($request->type == null){
            return Common::apiResponse (0,'missing param',null,422);
        }
        $list = Exchange::query ()->where ('type',$request->type)->orderBy ('diamonds')->get ();
        return Common::apiResponse (1,$user->coins,$list,200);
    }

    public function exchangeSave(Request $request){
        $user = $request->user ();
        $ex = Exchange::query ()->find ($request->item_id);
        if (!$ex) return Common::apiResponse (0,'not found',null,404);
        if ($user->is_host == 1) return Common::apiResponse (0,'not allowed',null,403);
        if ($user->coins < $ex->diamonds){
            return Common::apiResponse (0,'balance low',null,407);
        }
        try {
            DB::beginTransaction ();
            ExchangeLog::query ()->create (
                [
                    'user_id'=>$user->id,
                    'diamonds'=>$ex->diamonds,
                    'value'=>$ex->value,
                    'type'=>$ex->type
                ]
            );
            $user->coins -= $ex->diamonds;
            if ($ex->type == 0){
                $user->di += $ex->value;
            }elseif ($ex->type == 1){
                $user->gold +=  $ex->value;
            }
            $user->save();
            DB::commit ();
            return Common::apiResponse (1,$user->coins,$user->coins,200);

        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,$exception->getMessage (),null,400);
        }

    }


}
