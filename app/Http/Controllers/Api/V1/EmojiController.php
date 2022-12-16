<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Emoji;
use Illuminate\Http\Request;

class EmojiController extends Controller
{
    public function index(Request $request){
        $query = Emoji::query ()->where('enable',1);
        if ($request->pid){
            $query->where ('pid',$request->pid);
        }
        $data = $query->select ('id','pid','name','emoji','t_length','sort')->orderBy ('sort')->get ();
        return Common::apiResponse (1,'',$data);

    }

    public function show($id){
        $data = Emoji::query ()->select ('id','pid','name','emoji','t_length','sort')->find ($id);
        return Common::apiResponse (1,'',$data);
    }
}
