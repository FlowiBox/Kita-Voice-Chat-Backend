<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\ProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
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
    public function show($id)
    {
        $user = User::query ()->find ($id);
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
        if ($data['name']){
            $user->name = $data['name'];
        }
        if ($data['email']){
            $user->email = $data['email'];
        }
        if ($data['phone']){
            $user->phone = $data['phone'];
        }
        $user->save();
        $profile = $user->profile;
        if ($data['gender']){
            $profile->gender = $data['gender'] == 'female' ? 0 : 1;
        }
        if ($data['birthday']){
            $profile->birthday = $data['birthday'];
        }
        if ($data['province']){
            $profile->province = $data['province'];
        }
        if ($data['city']){
            $profile->city = $data['city'];
        }
        if ($data['country']){
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
}
