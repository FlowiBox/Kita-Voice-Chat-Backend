<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AgencyJoinReqResource;
use App\Http\Resources\Api\V1\AgencyResource;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $query =Agency::query ()->where ('status',1);
        if ($search){
            $query = $query->where ('name',$search);
        }
        $data = $query->get ();
        return Common::apiResponse (1,'',AgencyResource::collection ($data));
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $agency = Agency::query ()->find ($id);
        if ($agency){
            return Common::apiResponse (1,'',new AgencyResource($agency));
        }
        return Common::apiResponse (0,'agency not found');
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

    public function joinRequest(Request $request){
        $user_id = $request->user ()->id;
        $agency_id = $request->agency_id;
        if (!$request->agency_id) return Common::apiResponse (0,'missing params',null,422);
        $joind = $request->user ()->agency_id;
        if ($joind) return Common::apiResponse (0,'you are already under agency',null,405);
        $reqs_count = AgencyJoinRequest::query ()->where ('user_id',$user_id)->count ();
        if ($reqs_count >= 5){
            return Common::apiResponse (0,'you have +5 requests ,not allowed to request other more',444);
        }

        AgencyJoinRequest::query ()->create (
            [
                'user_id'=>$user_id,
                'agency_id'=>$agency_id
            ]
        );
        $reqs = AgencyJoinRequest::query ()->where ('user_id',$user_id)->get ();
        return Common::apiResponse (1,'request sent',AgencyJoinReqResource::collection ($reqs));
    }

    public function joinRequests(Request $request){
        $reqs = AgencyJoinRequest::query ()->where ('user_id',$request->user ()->id)->get ();
        return Common::apiResponse (1,'',AgencyJoinReqResource::collection ($reqs));
    }

}
