<?php

namespace App\Observers\Api\V1;

use App\Models\Room;

class RoomObserver
{

    public function created(Room $room)
    {

    }


    public function updated(Room $room)
    {

    }


    public function deleted(Room $room)
    {

    }


    public function restored(Room $room)
    {
        //
    }


    public function forceDeleted(Room $room)
    {
        //
    }


    public function creating(Room $room){

    }


    public function updating (Room $room){
        $this->resetRoomSession ($room);
    }


    public function saving(Room $room){

        $this->changeMode ($room);
        $this->resetRoomSession ($room);

    }

    public function changeMode($room){
        $mics = explode (',',$room->microphone);

        if ($room->mode == '1'){
            if (count ($mics) <= 10){
                $m = array_merge ($mics,['0','0','0','0','0','0','0','0']);
                $room->microphone = implode (',',$m);
            }
        }else{
            if (count ($mics) > 10){
                $m = array_slice ($mics,0,10);
                $room->microphone = implode (',',$m);
            }
        }
    }

    public function resetRoomSession($room){
        $owner_in = $room->is_afk;
        $visitors = explode (',',$room->room_visitor);
        if (count ($visitors) < 1 && $owner_in != 1){
            $room->session = 0;
        }
    }
}
