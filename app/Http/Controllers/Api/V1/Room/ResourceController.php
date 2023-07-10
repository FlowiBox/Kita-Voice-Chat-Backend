<?php

namespace App\Http\Controllers\Api\V1\Room;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditRoomRequest;
use App\Http\Resources\Api\V1\RoomResource;
use App\Models\Agency;
use App\Models\AgencySallary;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RequestBackgroundImage;
use App\Models\UserSallary;
use App\Repositories\Room\RoomRepoInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $repo;

    public function __construct (RoomRepoInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = $this->repo->all ($request);
        $user = $request->user ();
        foreach (Agency::all () as $agency){
            AgencySallary::query ()->updateOrCreate (
                [
                    'agency_id'=>$agency->id,
                    'month'=>Carbon::now ()->month,
                    'year'=>Carbon::now ()->year
                ],
                [
                    'agency_id'=>$agency->id,
                    'month'=>Carbon::now ()->month,
                    'year'=>Carbon::now ()->year,
                    'sallary'=>UserSallary::query ()
                        ->where ('month',Carbon::now ()->month)
                        ->where ('year',Carbon::now ()->year)
                        ->where ('user_agency_id',$agency->id)
                        ->sum ('agency_sallary')
                ]
            );
            $agency->salary;
        }

        return Common::apiResponse (true,'',RoomResource::collection ($result),200,Common::getPaginates ($result));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request['show']=true;
        $request['numid']=rand (111111,999999);
        try {
            $room = Room::query ()->where('uid',$request->user ()->id)->first ();
            if ($room){
                return Common::apiResponse (true,'you are already have a room',new RoomResource($room),200);
            }
            $room = $this->repo->create (array_merge($request->all (),['uid'=>$request->user ()->id]));
            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
                $room->save();
            }
            return Common::apiResponse (true,'created',new RoomResource($room),200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,'failed',null,400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,$id)
    {
        $request['show'] = true;
        $room = Room::find($id);
        if (!$room){
            return Common::apiResponse(0,'not found',null,404);
        }
        return Common::apiResponse (true,'',new RoomResource($room),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditRoomRequest $request, $id)
    {



        try {
            $room = $this->repo->find ($id);
            if(!$room){
                return Common::apiResponse (false,'not found',null,404);
            }
            // return  $request->user ()->id;
            if ($room->uid != $request->user ()->id && !in_array ($request->user ()->id,explode (',',$room->room_admin))){
                return Common::apiResponse (false,'not allowed',null,403);
            }
            if ($request->room_name){
                $room->room_name = $request->room_name;
            }

            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
            }

            if ($request->free_mic){
                $room->free_mic = $request->free_mic;
            }

            if ($request->room_intro){
                $room->room_intro = $request->room_intro;
            }

            if ($request->room_pass){
                $room->room_pass = $request->room_pass;
            }

            if ($request->room_type){
                if (!RoomCategory::query ()->where ('id',$request->room_type)->where ('enable',1)->exists ()) return Common::apiResponse (0,'type not found',null,404);
                $room->room_type = $request->room_type;
            }

            if ($request->room_class){
                if (!RoomCategory::query ()->where ('id',$request->room_class)->where ('enable',1)->exists ()) return Common::apiResponse (0,'class not found',null,404);
                $room->room_type = $request->room_type;
            }
            $background_me = '';


            if ($request->room_background){
                /*if (!Background::query ()->where ('id',$request->room_background)->where ('enable',1)->exists ()){
                    return Common::apiResponse (0,'background not found',null,404);
                }*/
                if($request->change == 'app'){
                    $room->room_background = $request->room_background;

                    RequestBackgroundImage::query()->where('owner_room_id',$room->uid)->where('status',1)->update(['status' => 3]);

                }
                if($request->change == 'me'){

                    RequestBackgroundImage::query()->where('owner_room_id',$room->uid)->where('id','!=',$request->room_background)->where('status',1)->update(['status' => 3]);
                    $background_update = RequestBackgroundImage::where('id',$request->room_background)->first();
                    $background_update->status = 1;
                    $background_update->save();
                    $background_me = $background_update->img;
                    $room->room_background = null;
                }

            }
              //    $this->repo->save ($room);
            
        $room->save();
            // if($room->save ()){
            // return "ايوووه يا باشا ";
            //             }else{
            // return "لا يا باشا ";

            //             }


            $request['owner_id'] = $room->uid;

            $data = [
                "messageContent"=>[
                    "message"=>"changeBackground",
                    "imgbackground"=>$room->room_background?:@$background_me,
                    "roomIntro"=>$room->room_intro?:"",
                    "roomImg"=>$room->room_cover?:"",
                    "room_type"=>@$room->myType->name?:"",
                    "room_name"=>@$room->room_name?:""
                ]
            ];
            // $json = json_encode ($data);
            // Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            // $request->is_update = true;
            return $this->enter_room2 ($request);

        }catch (\Exception $exception){
            return Common::apiResponse (false,'failed',null,400);
        }
    }

}
