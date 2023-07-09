<?php

namespace App\Jobs;

use App\Helpers\Common;
use Illuminate\Bus\Queueable;
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
        $this->user   = $user;
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
        $this->user->withoutAppends = false;
        $d                          = [
            "messageContent" => [
                "message"    => "userEntro",
                "entroImg"   => $this->user->intro ?? '',
                "entroImgId" => $this->user->dress_3 ? (string)$this->user->dress_3 : "",
                'userName'   => $this->user->name ?: $this->user->nickname,
                'userImge'   => $this->user->avatar,
                'vip'        => $this->hasVip,
                'uid'        => $this->user->id
            ]
        ];

        $json = json_encode($d);

        Common::sendToZego('SendCustomCommand', $this->roomId, $this->user->id, $json);
        if (!Common::hasInPack($this->user->id, 17, true)) {
            Common::sendToZego_2('SendBroadcastMessage', $this->roomId, $this->user->id, $this->user->name, ' انضم للغرفة');
        }
    }
}
