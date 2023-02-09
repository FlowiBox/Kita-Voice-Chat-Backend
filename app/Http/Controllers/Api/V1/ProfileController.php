<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\ProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(Request $request,$id)
    {
        $me = $request->user ();
        $user = User::query ()->find ($id);
        if ($me->id != $user->id){
            $user->profileVisits()->sync([$me->id]);
        }
        if($user){
            return Common::apiResponse (true,'',new UserResource($user),200);
        }
        return Common::apiResponse (false,'user not found',[],404);
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
    public function update(ProfileRequest $request)
    {
        $data = $request->all ();
        $user = $request->user ();
        if ($request->name){
            $user->name = $data['name'];
        }
        if ($request->email){
            $user->email = $data['email'];
        }
        if ($request->phone){
            $user->phone = $data['phone'];
        }
        if ($request->nickname){
            $user->nickname = $request->nickname;
        }
        if ($request->country_phone_code){
            $country = Country::query ()->where ('phone_code',$request->country_phone_code)->first ();
            if ($country){
                $user->country_id = $country->id;
            }
        }
        if ($request->bio){
            $user->bio = $request->bio;
        }
        if ($request->chat_id){
            $user->chat_id = $request->chat_id;
        }

        if ($request->notification_id){
            $user->notification_id = $request->notification_id;
        }

        $user->save();
        $profile = $user->profile;
        if ($request->gender){
            $profile->gender = $data['gender'] == 'female' ? 0 : 1;
        }
        if ($request->birthday){
            $profile->birthday = $data['birthday'];
        }
        if ($request->province){
            $profile->province = $data['province'];
        }
        if ($request->city){
            $profile->city = $data['city'];
        }
        if ($request->country){
            $profile->country = $data['country'];
        }
        if ($request->hasFile ('image')){
            $img = $request->file ('image');
            $image = Common::upload ('profile',$img);
            $profile->avatar = $image;
        }
        $profile->save();
        $out = new UserResource($user);
        return Common::apiResponse (true,'profile updated successfully',$out,200);
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

    public function myProfileVisitorsList(Request $request){
        $user = $request->user();
        $visitors = UserResource::collection ($user->profileVisits);
        return Common::apiResponse (1,'',$visitors);
    }
}
