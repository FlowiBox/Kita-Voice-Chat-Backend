<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HelperTraits\PusherTrait;
use App\Models\Room;

class UpdateRoomUserNowCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-room-user-now:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update user in room after 60 seconds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        //\Log::info("Testing Cron is Running ... !");
     
        $rooms_now_live = PusherTrait::getIdRoomCountUserFromPresenceChannel();
        $rooms_now_live = collect($rooms_now_live)->sortBy(function($item, $key) {
            return $item['owner_room_id'];
        });   
        $rooms_owner_ids = collect($rooms_now_live)->pluck('owner_room_id');
        $roomUpdate = Room::whereIn('uid',$rooms_owner_ids)->orderBy('uid')->get();
        foreach($roomUpdate as $key => $room)
        {
            Room::where('id',$room->id)->first()->update([
                'count_room_socket' => $rooms_now_live[$key]['count_user']
            ]);
        }
        $roomUpdate2 = Room::whereNotIn('uid',$rooms_owner_ids)->where('room_status',1)->get();
        foreach($roomUpdate2 as $key => $room)
        {
            Room::where('id',$room->id)->first()->update([
                'count_room_socket' => 0
            ]);
        }
      
        //$this->info('update-room-user-now:cron Command Run Successfully !');
    }
}