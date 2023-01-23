<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FamilyResource;
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

        $top = array_slice ($data,0,3);
        $top[0]=@$top[0]?:null;
        $top[1]=@$top[1]?:null;
        $top[2]=@$top[2]?:null;
        $other = array_slice ($data,3);
        $top = FamilyResource::collection ($top);
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

        if ($request->hasFile ('image')){
            $data['image'] = Common::upload ('families',$request->file ('image'))?:' ';
        }

        try {
            DB::beginTransaction ();
            $family = Family::query ()->create ($data);
            FamilyUser::query ()->create (
                [
                    'user_id '=>$user->id,
                    'family_id'=>$family->id,
                    'user_type'=>2,
                    'status'=>1,
                ]
            );
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
        if ($user->id != $family->user_id) return Common::apiResponse (0,'not allowed',null,403);
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
        $family = Family::find($id);
        if ($user->id != $family->user_id) return Common::apiResponse (0,'not allowed');
        Family::query ()->find ($id)->delete ();
        FamilyUser::query ()->where ('family_id',$id)->delete ();
        User::query ()->where ('family_id',$id)->update (['family_id'=>null]);
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
            $fu1->status = 1;
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
}
