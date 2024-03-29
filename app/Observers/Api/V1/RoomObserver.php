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
        $this->changeMode ($room);
        $this->resetRoomSession ($room);
        $v = $room->room_visitor;
        $av = explode (',',$v);
        $room->visitor_count = count ($av);
    }


    public function saving(Room $room){
        $this->changeMode ($room);
        $this->resetRoomSession ($room);
        $v = $room->room_visitor;
        $av = explode (',',$v);
        $room->visitor_count = count ($av);
    }

    public function changeMode($room){
        $mics = explode (',',$room->microphone);

        if ($room->mode == '1'){//16 seats
            if (count ($mics) <= 10){
                $m = array_merge ($mics,['0','0','0','0','0','0','0','0']);
                $room->microphone = implode (',',$m);
            }
        }elseif ($room->mode == '2'){//12 seats
            if (count ($mics) <= 10){
                $m = array_merge ($mics,['0','0','0','0']);
                $room->microphone = implode (',',$m);
            }else{
                $m = array_slice ($mics,0,14);
                $room->microphone = implode (',',$m);
            }
        }
        else{// 8 seats
            if (count ($mics) > 10){
                $m = array_slice ($mics,0,10);
                $room->microphone = implode (',',$m);
            }
        }
    }

    public function resetRoomSession($room){
        $owner_in = $room->is_afk;
        if (!$room->room_visitor && $owner_in != 1){

            $room->session = 0;
            $room->room_speak = null;
        }

    }
}
