<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\EditRoomRequest;
use App\Http\Resources\Api\V1\RoomResource;
use App\Models\Room;
use App\Repositories\Room\RoomRepo;
use App\Repositories\Room\RoomRepoInterface;
use App\Traits\HelperTraits\RoomTrait;
use Illuminate\Http\Request;

class RoomController extends Controller
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
        return Common::apiResponse (true,'',RoomResource::collection (CacheHelper::get ('rooms',$this->repo->all ($request))),200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoomRequest $request)
    {
        try {
            $this->repo->create (array_merge($request->validated (),['uid'=>$request->user ()->id]));
            return Common::apiResponse (true,'created',null,200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,$exception->getMessage (),null,$exception->getCode ());
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
        return Common::apiResponse (true,'',new RoomResource($this->repo->find ($id)),200);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditRoomRequest $request, $id)
    {

        try {
            $room = $this->repo->find ($id);
            if ($room->uid != $request->user ()->id && !in_array ($request->user ()->id,explode (',',$room->room_admin))){
                return Common::apiResponse (false,'not allowed',null,401);
            }
            if ($request->room_name){
                $room->room_name = $request->room_name;
            }
            if ($request->room_mics){
                $room->microphone = $request->room_mics;
            }
            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
            }
            if ($request->hasFile ('room_background')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_background'));
            }
            $this->repo->save ($room);
            return Common::apiResponse (true,'updated',new RoomResource($room),200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,$exception->getMessage (),null,500);
        }
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





}
