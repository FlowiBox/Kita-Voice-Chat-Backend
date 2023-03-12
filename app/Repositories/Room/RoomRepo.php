<?php
namespace App\Repositories\Room;
use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoomRepo implements RoomRepoInterface {

    public $model;
    public function __construct (Room $model)
    {
        $this->model = $model;
    }

    public function all ( $req )
    {


        $result = Room::select('rooms.*', DB::raw('SUM(gift_logs.giftPrice * gift_logs.giftNum) as total_price'))
            ->where('rooms.room_status', 1)->where(function ($q){
                $q->where('rooms.is_afk', 1)->orWhere('rooms.room_visitor', '!=', '');
            })->where(function ($q) use ($req){
                if ($search = $req->search){
                    $q->where('rooms.room_name', $search)->orWhere('rooms.numid', $search)->orWhere('rooms.uid', $search);
                }
                if ($req->country_id){
                    $q->whereHas('owner', function ($q) use ($req){
                        $q->where('country_id', $req->country_id);
                    });
                }
                if ($req->class){
                    $q->where('room_class', $req->class);
                }
                if ($req->type){
                    $q->where('room_type', $req->type);
                }
            })->leftJoin('gift_logs', function ($join) {
                $join->on('rooms.uid', '=', 'gift_logs.roomowner_id')
                    ->where('gift_logs.created_at', '>', now()->subHour());
            })
            ->orderByDesc('total_price');



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
