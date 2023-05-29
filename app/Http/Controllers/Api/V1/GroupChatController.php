<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\GroupChatRequest;
use App\Http\Resources\Api\V1\GroupChatResource;
use App\Models\User;
use App\Models\GroupChat;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = GroupChat::with('user')->orderBy('created_at','DESC')->paginate(10);
        $costGroupChat = Common::getConfig('group_chat');
        return response()->json(['price_message'=> $costGroupChat , 'data' => GroupChatResource::collection($data,200)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->text) return Common::apiResponse (0,'missing params',null,422);
        $user = $request->user ();
        $costGroupChat = Common::getConfig('group_chat');
        if(!$costGroupChat)
        {
            return Common::apiResponse (0,'not found params (group_chat) in config dashboard',null,404);
        }
        if($user->di < $costGroupChat){
            return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
        }else{
            $user->di = ($user->di - $costGroupChat);
            $user->save();
        }
        $groupChat = new GroupChat();
        $groupChat->text = $request->text;
        $groupChat->user_id = $user->id;
        $groupChat->save();
        return Common::apiResponse (1,'created done',null, 201);
    }
}
