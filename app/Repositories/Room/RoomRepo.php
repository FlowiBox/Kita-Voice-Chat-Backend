<?php
namespace App\Repositories\Room;
use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Models\Room;
use App\Models\User;

class RoomRepo implements RoomRepoInterface {

    public $model;
    public function __construct (Room $model)
    {
        $this->model = $model;
    }

    public function all ( $req )
    {
        $result = $this->model->where(function ($q) use ($req){
            if ($search = $req->search){
                $q->where('name',$search)->orWhere('id',$search);
            }
            if ($req->country_id){
                $q->whereHas('owner',function ($q) use ($req){
                    $q->where('country_id',$req->country_id);
                });
            }
        })->orderBy($req->ord?:'id',$req->sort?:'DESC');

        if ($pp = $req->pp){ // pp = perPage
            return $result->paginate($pp);
        }
        return $result->get();
    }

    public function find ( $id )
    {
        return $this->model->find($id);
    }

    public function create ( $data )
    {
        $this->model->create($data);
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
