<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function official_messages(Request $request)
    {
        $user_id = $request->user ()->id;
        $page = $request->page ?: 1;
        if (!$user_id) return Common::apiResponse (0, 'un_auth');
//        $ids = DB::table('off_reads')->where('user_id', $user_id)
//            ->where('is_read', 2)
//            ->select('off_id');
        $data = DB::table('official_messages')
            // ->where('id','not in',$ids)
            ->where('user_id', 'in', [0, $user_id])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($data as $k => &$v) {
            $v->url = $v->url ?: '';
            $v->is_read = DB::table('off_reads')->where('user_id', $user_id)->where('off_id', $v->id)->value('id') ? 1 : 0;
            //Mark unread messages as read
            if ($v->is_read == 0) {
                $arr['off_id'] = $v->id;
                $arr['user_id'] = $user_id;
                $arr['addtime'] = time();
                DB::table('off_reads')->insert($arr);
            }
        }
        return Common::apiResponse(1, '', $data);
    }
}
