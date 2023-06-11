<?php
namespace App\Repositories\Room;
use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Models\EnteredRoom;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Traits\HelperTraits\PusherTrait;

class RoomRepo implements RoomRepoInterface {

    public $model;
    public function __construct (Room $model)
    {
        $this->model = $model;
    }

    public function all ( $req )
    {
        $rooms_now_live = PusherTrait::getIdRoomCountUserFromPresenceChannel();
        $rooms_now_live = collect($rooms_now_live)->sortBy(function($item, $key) {
            return $item['owner_room_id'];
        });
        //dd($rooms_now_live);    
        $rooms_owner_ids = collect($rooms_now_live)->pluck('owner_room_id');
        //dd($rooms_owner_ids);
        $roomUpdate = Room::whereIn('uid',$rooms_owner_ids)->orderBy('uid')->get();
        //dd($roomUpdate);
        //dd($roomUpdate);
        foreach($roomUpdate as $key => $room)
        {
            Room::where('id',$room->id)->first()->update([
                'count_room_socket' => $rooms_now_live[$key]['count_user']
            ]);
        }
        $roomUpdate2 = Room::whereNotIn('uid',$rooms_owner_ids)->where('count_room_socket','>=',1)->where('room_status',1)->update([
            'count_room_socket' => 0
        ]);
        /*foreach($roomUpdate2 as $key => $room)
        {
            Room::where('id',$room->id)->first()->update([
                'count_room_socket' => 0
            ]);
        }*/

        $result = $this->model
            ->orderBy('top_room','DESC')->where('count_room_socket','!=',0)
            ->where('room_status',1)
            /*->where(function ($q){
                $q->where('is_afk',1);
            })*/
            ->where(function ($q) use ($req){
            if ($search = $req->search){
                $q->where('room_name',$search)->orWhere('numid',$search)->orWhere('uid',$search);
            }
            if($req->socket == true)
            {
                $q->where('count_room_socket','!=',0);
            }
            if ($req->country_id){
                $q->whereHas('owner',function ($q) use ($req){
                    $q->where('country_id',$req->country_id);
                });
            }
            if ($req->class){
                $q->where('room_class',$req->class);
            }
            if ($req->type){
                $q->where('room_type',$req->type);
            }
        });

        if ($req->filter == 'boss'){
            $arr = EnteredRoom::query ()
                ->where ('uid',Common::getConf ('boss_id'))
                ->orderByDesc ('entered_at')
                ->pluck ('rid')
                ->toArray ();
            $result->whereIn('id',$arr)->orderByRaw(DB::raw("FIELD(id, " . implode(',', $arr) . ")"));
        }
        elseif ($req->filter == 'trend'){
            $result->orderByDesc('session');
        }
        elseif ($req->filter == 'popular'){
            $result->orderByDesc('count_room_socket');
        }
        elseif ($req->filter == 'festival'){
            $result->orderByDesc('session')
                ->orderByDesc('count_room_socket')
            ;
        }

        else{
            $result->orderByDesc('hour_hot');
        }




        if ($pp = $req->pp){ // pp = perPage
            return $result->paginate($pp);
        }
        
        return $result->paginate(10);
        
    }

    public function find ( $id )
    {
        return $this->model->find($id);
    }

    public function create ( $data )
    {
        return $this->model->create($data);
    }

    public function update ( $req , $id )
    {
        // TODO: Implement update() method.
    }

    public function delete ( $id )
    {
        // TODO: Implement delete() method.
    }

    public function save ($model)
    {
        CacheHelper::forget('rooms');
        $model->save ();
        return $model;
    }
}
