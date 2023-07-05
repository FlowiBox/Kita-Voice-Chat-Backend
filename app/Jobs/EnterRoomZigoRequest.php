<?php

namespace App\Jobs;

use App\Helpers\Common;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnterRoomZigoRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $roomId;
    /**
     * @var false
     */
    private $hasVip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $roomId, $hasVip = false)
    {
        //
        $this->user = $user;
        $this->roomId = $roomId;
        $this->hasVip = $hasVip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $d = [
            "messageContent"=>[
                "message"=>"userEntro",
                //                "entroImg"=>Common::getUserDress($user->id,$user->dress_3,6,'img2')?:Common::getUserDress($user->id,$user->dress_3,6,'img1'),
                "entroImgId"=>$this->user->dress_3?(string)$this->user->dress_3:"",
                'userName'=>$this->user->name?:$this->user->nickname,
                'userImge'=>$this->user->avatar,
                'vip'=>$this->hasVip
            ]
        ];

        $json = json_encode ($d);

        Common::sendToZego ('SendCustomCommand',$this->roomId ,$this->user->id,$json);
        if (!Common::hasInPack ($this->user->id,17,true)){
            Common::sendToZego_2 ('SendBroadcastMessage',$this->roomId,$this->user->id,$this->user->name,' انضم للغرفة');
        }
    }
}
