<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FamilyResource;
use App\Http\Resources\Api\V1\FamilyUserResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\User;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $query =DB::table ('families')->where ('status',1);
        if ($search){
            $query = $query->where ('name',$search);
        }
        $data = $query->get ();
        return Common::apiResponse (1,'',FamilyResource::collection ($data));
    }

    public function ranking(Request $request){
        $time = $request->time;
        if ($time == 'today'){
            $query =DB::table ('today_family_views')->where ('status',1)->whereNotNull ('rank')->orderByDesc ('rank');
        }
        elseif ($time == 'week'){
            $query =DB::table ('week_family_views')->where ('status',1)->whereNotNull ('rank')->orderByDesc ('rank');
        }
        elseif ($time == 'month'){
            $query =DB::table ('month_family_views')->where ('status',1)->whereNotNull ('rank')->orderByDesc ('rank');
        }
        $data = $query->get ()->toArray ();

        $em = [
            'id'=>0,
            'name'=>'',
            'introduce'=>'',
            'image'=>'',
            'notice'=>'',
            'max_num_of_members'=>0,
            'rank'=>0,
            'owner'=>new \stdClass(),
            'am_i_member'=>false,
            'am_i_owner'=>false,
            'am_i_admin'=>false,
            'members'=>[]
        ];

        $top = array_slice ($data,0,3);
        $top[0]=@$top[0]?:$em;
        $top[1]=@$top[1]?:$em;
        $top[2]=@$top[2]?:$em;
        $other = array_slice ($data,3);
        $other = FamilyResource::collection ($other);
        return Common::apiResponse (1,'',[
            'top'=>$top,
            'other'=>$other
        ]);
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
    public function store(Request $request)
    {
        $user = $request->user ();
        $ex = Family::query ()->where ('user_id',$user->id)->exists ();
        if ($ex) return Common::apiResponse (0,'already have family',null,405);
        $data = [
            'name'=>$request->name,
            'introduce'=>$request->introduce,
            'notice'=>$request->notice,
            'user_id'=>$user->id,
            'num'=>20,
            'is_success'=>1,
        ];


        $img = null;
        if ($request->hasFile ('image')){
            $img = Common::upload ('families',$request->file ('image'));
        }

        try {
            DB::beginTransaction ();
            $family = new Family();
            $family->name = $request->name;
            $family->introduce = $request->introduce;
            $family->notice = $request->notice;
            $family->user_id = $user->id;
            $family->num = 20;
            $family->image = $img?:'';
            $family->is_success = 1;
            $family->save ();
            $family_user = new FamilyUser();
            $family_user->user_id = $user->id;
            $family_user->family_id = $family->id;
            $family_user->user_type = 2;
            $family_user->status = 1;
            $family_user->save ();
            $user->family_id = $family->id;
            $user->save();
            DB::commit ();
        }catch (\Exception $exception){
            DB::rollBack ();
            dd ($exception);
            return Common::apiResponse (0,'failed',null,400);
        }


        return Common::apiResponse (1,'created',new FamilyResource($family));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $family = Family::query ()->find ($id);
        if (!$family) return Common::apiResponse (0,'not found',null,404);
        return Common::apiResponse (1,'',new FamilyResource($family));
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
    public function update(Request $request, $id)
    {
        $user = $request->user ();
        $family = Family::find($id);
        $is_admin = FamilyUser::query ()
            ->where ('user_type',1)
            ->where ('user_id',$user->id)
            ->where ('family_id',$family->id)
            ->where ('status',1)
            ->exists ();
        if (($user->id != $family->user_id) || !$is_admin) return Common::apiResponse (0,'not allowed',null,403);
        if (!$family) return Common::apiResponse (0,'not found',null,404);
        if ($request->name){$family->name = $request->name ;}
        if ($request->introduce){$family->introduce = $request->introduce ;}
        if ($request->notice){$family->notice = $request->notice ;}
        if ($request->hasFile ('image')){
            $family->image = Common::upload ('families',$request->file ('image'));
        }
        $family->save();
        return Common::apiResponse (1,'',new FamilyResource($family));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,$id)
    {
        $user = $request->user ();
        $family = Family::query()->where('user_id',$user->id)->first ();
        if (!$family) return Common::apiResponse (0,'not found',null,404);
        if ($user->id != $family->user_id) return Common::apiResponse (0,'not allowed',null,403);
        DB::beginTransaction ();
        try {
            FamilyUser::query ()->where ('family_id',$family->id)->delete ();
            User::query ()->where ('family_id',$id)->update (['family_id'=>null]);
            $family->delete ();
            DB::commit ();
            return Common::apiResponse (1,'success',null,201);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'failed',null,400);
        }

    }


    public function join(Request $request){
        $user = $request->user ();
        $family = Family::query ()->find ($request->family_id);
        if(!$family){
            return Common::apiResponse (0,'family not found',null,404);
        }
        if ($family->id == $user->family_id){
            return Common::apiResponse (0,'already joined',null,405);
        }
        $fu = FamilyUser::query ()->where ('user_id',$user->id)->where ('family_id',$family->id)->where ('status',1)->exists ();
        if ($fu){
            $user->family_id = $family->id;
            $user->save();
            return Common::apiResponse (0,'already joined',null,405);
        }

        try {
            DB::beginTransaction ();
            $fu1 = new FamilyUser();
            $fu1->user_id = $user->id;
            $fu1->family_id = $family->id;
            $fu1->user_type = 0;
            $fu1->status = 0;
            $user->family_id = $family->id;
            $fu1->save ();
            $user->save();
            DB::commit ();
            return Common::apiResponse (1,'',new FamilyResource($family));
        }catch (\Exception $exception){
            DB::rollBack ();
            dd ($exception);
            return Common::apiResponse (0,'failed',null,400);
        }


    }

    public function req_list(Request $request){
        $family = Family::query ()->where ('user_id',$request->user ()->id)->first ();
        if (!$family){
            $fu = FamilyUser::query ()->where ('user_id',$request->user ()->id)->where ('user_type',1)->first ();
            if ($fu){
                $family = Family::query ()->where ('id',$fu->family_id)->first ();
            }

        }
        if (!$family) return Common::apiResponse (0,'not found',null,404);
        $req = FamilyUserResource::collection (FamilyUser::query ()->where ('family_id',$family->id)->where ('user_id','!=',$request->user ()->id)->get ());
        return Common::apiResponse (1,'',$req,200);
    }

    public function accdie(Request $request){

        if (!$request->status || !$request->req_id) return Common::apiResponse (0,'missing params',null,422);
        $req = FamilyUser::query ()->find ($request->req_id);
        if (!$req) return Common::apiResponse (0,'not found',null,404);
        DB::beginTransaction ();
        try {
            $req->status = $request->status;
            $req->save ();
            $user = User::find($req->user_id);
            if ($user){
                $user->family_id = $req->family_id;
                $user->save();
            }
            DB::commit ();
            $reqs = FamilyUserResource::collection (FamilyUser::query ()->where ('family_id',$req->family_id)->where ('user_id','!=',$request->user ()->id)->get ());
            return Common::apiResponse (1,'',$reqs,200);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'failed',null,400);
        }

    }

    public function changeReqType(Request $request){
        if (!$request->type || !$request->user_id || !$request->family_id || !in_array ($request->type,['0','1'])){
            return Common::apiResponse (0,'invalid data',null,422);
        }
        $req = FamilyUser::query ()
            ->where ('user_id',$request->user_id)
            ->where ('family_id',$request->family_id)
            ->where ('status',1)->first ();
        if (!$req) return Common::apiResponse (0,'not found',null,404);
        $req->user_type = $request->type;
        $req->save ();
        $reqs = FamilyUserResource::collection (FamilyUser::query ()->where ('family_id',$req->family_id)->where ('user_id','!=',$request->user ()->id)->get ());
        return Common::apiResponse (1,'',$reqs,200);
    }

    public function removeUser(Request $request){
        $me = $request->user ();
        $is_admin = FamilyUser::query ()
            ->where ('user_type',1)
            ->where ('user_id',$me->id)
            ->where ('family_id',$request->family_id )
            ->where ('status',1)
            ->exists ();
        if (!$request->family_id || !$request->user_id) return Common::apiResponse (0,'missing params',null,422);
        $family = Family::query ()->find ($request->family_id);
        $user = User::query ()->find ($request->user_id);
        if (!$family || !$user){
            return Common::apiResponse (0,'not found',null,404);
        }
        if (!$is_admin && ($family->user_id != $me->id)) return Common::apiResponse (0,'not allowed',null,403);
        DB::beginTransaction ();
        try {
            $user->family_id;
            FamilyUser::query ()->where ('family_id',$family->id)->where ('user_id',$request->user_id)->delete ();
            $user->save ();
            DB::commit ();
            return Common::apiResponse (1,'success', new FamilyResource(Family::find($family->id)),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'failed',null,400);
        }
    }


}
