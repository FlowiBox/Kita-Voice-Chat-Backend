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




    //搜索
    public function merge_search()
    {
        $keywords = $this->request->request('keywords');
        $user_id = $this->user_id;
        if (!$keywords || !$user_id) $this->ApiReturn(0, 'Missing parameters');
        $search = DB::name('search_history')
            ->where('user_id', $user_id)
            ->where('search', $keywords)
            ->where('type', 2)
            ->select();
        if (!$search) {
            $info['search'] = $keywords;
            $info['user_id'] = $user_id;
            $info['addtime'] = time();
            DB::name('search_history')->insert($info);
        }
        //用户
        $user = array_slice($this->user_search_hand($user_id, $keywords), 0, 2);
        //房间
        $rooms = array_slice($this->room_search_hand($user_id, $keywords), 0, 2);
        //动态
        $dynamics = array_slice($this->dynamics_search_hand($user_id, $keywords), 0, 2);
        //游戏分类
        $gmskill = array_slice($this->gmskill_search_hand($user_id, $keywords), 0, 2);
        $arr['gmskill'] = $gmskill;

        $arr['user'] = $user;
        $arr['rooms'] = $rooms;
        $arr['dynamics'] = $dynamics;
        $this->ApiReturn(1, '', $arr);

    }


    //搜索记录
    public function searhList()
    {
        $user_id = $this->user_id;
        if (!$user_id) $this->ApiReturn(0, '缺少参数');
        $hot = DB::name('search_history')->field(['id', 'search'])->where('type', 1)->order('sort', 'desc')->select();
        $history = DB::name('search_history')->field(['id', 'search'])->where('type', 2)->where('user_id', $user_id)->select();
        $data['hot'] = $hot;
        $data['histor'] = $history;
        $this->ApiReturn(1, '', $data);
    }

    //清空搜索记录
    public function cleanSarhList()
    {
        $user_id = $this->user_id;
        if (!$user_id) $this->ApiReturn(0, '缺少参数');
        $res = DB::name('search_history')->where('type', 2)->where('user_id', $user_id)->delete();
        if ($res) {
            $this->ApiReturn(1, '清空成功');
        } else {
            $this->ApiReturn(0, '清空失败');
        }

    }


    //搜索用户
    public function user_search_hand($user_id = null, $keywords = null, $page = 1)
    {
        if (!$user_id || !$keywords) return [];
        //用户
        $whereOr = [
            'id' => $keywords,
            'status' => 1,
        ];
        $user = DB::name('users')
            ->where('nickname', 'like', '%' . $keywords . '%')
            ->where(['status' => 1])
            ->whereOr(function ($query) use ($whereOr) {
                $query->where($whereOr);
            })
            ->page($page, 10)
            ->field(['id', 'headimgurl', 'nickname', 'sex'])
            ->select();
        foreach ($user as $ku => &$vu) {
            $vu['headimgurl'] = $this->auth->setFilePath($vu['headimgurl']);
            $vu['is_follow'] = DB::name('follows')->where('user_id', $user_id)->where('followed_user_id', $vu['id'])->where('status', 1)->value('id') ? 1 : 0;
        }

        unset($vu);
        //return json_decode($user,true);
        return $user;
    }

    //搜索房间
    public function room_search_hand($user_id = null, $keywords = null, $page = 1)
    {
        if (!$user_id || !$keywords) return [];
        //用户
        $whereOr = [
            'rooms.numid|rooms.uid' => $keywords,
            'users.status' => 1,
        ];
        $rooms = DB::name('rooms')
            ->alias('rooms')
            ->where('rooms.room_name', 'like', '%' . $keywords . '%')
            ->where(['users.status' => 1])
            // ->whereOr('rooms.numid',$keywords)
            ->join('users', 'rooms.uid=users.id', 'left')
            ->field(['rooms.room_name', 'rooms.uid', 'rooms.numid', 'rooms.hot', 'rooms.room_cover',
                        'users.headimgurl', 'users.nickname', 'users.sex'])
            ->whereOr(function ($query) use ($whereOr) {
                $query->where($whereOr);
            })
            ->order('rooms.hot', 'desc')
            ->page($page, 10)
            ->select();
        foreach ($rooms as $k => &$vr) {
            $vr['headimgurl'] = $this->auth->setFilePath($vr['headimgurl']);
            $vr['room_name'] = urldecode($vr['room_name']);
            $vr['hot'] = room_hot($vr['hot']);
            $vr['room_cover'] = $this->auth->setFilePath($vr['room_cover']);
        }
        unset($vr);
        //return json_decode($rooms,true);
        return $rooms;
    }

    //搜索动态
    public function dynamics_search_hand($user_id = null, $keywords = null, $page = 1)
    {
        if (!$user_id || !$keywords) return [];
        $start = strpos($keywords, '#');
        if ($start !== false) {
            $name = substr($keywords, $start + 1);
            $tags = DB::name('labels')->where('name', $name)->value('id');
        } else {
            $tags = 'null';
        }
        $whereOr = [
            'dynamics.tags' => ['like', '%' . $tags . '%'],
            'users.status' => 1,
        ];
        $dynamics = DB::name('dynamics')
            ->alias('dynamics')
            ->where('dynamics.content', 'like', '%' . $keywords . '%')
            ->where(['users.status' => 1])
            ->whereOr(function ($query) use ($whereOr) {
                $query->where($whereOr);
            })
            ->join('users', 'dynamics.user_id=users.id')
            ->field(['dynamics.id', 'dynamics.share', 'dynamics.audio_time', 'dynamics.user_id', 'dynamics.image', 'dynamics.audio', 'dynamics.video',
                        'dynamics.content', 'dynamics.praise', 'dynamics.tags', 'dynamics.addtime', 'users.headimgurl', 'users.nickname', 'users.sex'])
            ->page($page, 10)
            ->select();
        if (!$dynamics) {
            return [];
        } else {
            $dynamics = $this->dataFormat($dynamics, $user_id);
            //return json_decode($dynamics,true);
            return $dynamics;
        }
    }

    /**
     * 搜索用户,房间,动态
     * @param int               page            页码
     * @param str               type            搜索类型 user:用户,room:房间,dynamic:动态
     */
    public function search_all()
    {
        $page = input('page/d', 1);
        $info['user_id'] = $this->user_id;
        $info['type'] = input('type/s', '');
        $info['keywords'] = input('keywords/s', '');
        if (!$info['keywords'] || !$info['type']) $this->ApiReturn(0, '缺少参数');
        if ($info['type'] == 'user') {
            $data = $this->user_search_hand($info['user_id'], $info['keywords'], $page);
        } elseif ($info['type'] == 'room') {
            $data = $this->room_search_hand($info['user_id'], $info['keywords'], $page);
        } elseif ($info['type'] == 'dynamic') {
            $data = $this->dynamics_search_hand($info['user_id'], $info['keywords'], $page);
        } else {
            $this->ApiReturn(0, '参数错误');
        }
        $this->ApiReturn(1, '', $data);
    }
}
