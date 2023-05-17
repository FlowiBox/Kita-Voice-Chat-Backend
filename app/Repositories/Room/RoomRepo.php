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
        $result = $this->model->orderBy('top_room','DESC')->where('room_status',1)->where(function ($q){
            $q->where('is_afk',1)->orWhere('room_visitor','!=','');
        })->where(function ($q) use ($req){
            if ($search = $req->search){
                $q->where('room_name',$search)->orWhere('numid',$search)->orWhere('uid',$search);
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

        if ($req->filter == 'trend'){
            $result->orderByDesc('session');
        }
        elseif ($req->filter == 'popular'){
            $result->orderByDesc('visitor_count');
        }
        elseif ($req->filter == 'festival'){
            $result->orderByDesc('session')
                ->orderByDesc('visitor_count')
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
