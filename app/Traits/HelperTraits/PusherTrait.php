<?php
namespace App\Traits\HelperTraits;

use App\Helpers\Common;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

trait PusherTrait
{
    public static function getConfigPusher()
    {
        $connection = config( 'broadcasting.connections.pusher' );
        $pusher = new Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            [
                'cluster' => $connection['options']['cluster'],
                'useTLS'  => TRUE,
                'host'    => $connection['options']['host'],
                'port'    => '6001',
                'scheme'  => 'http',
                'debug'   => TRUE,
            ]
        );

        return $pusher;
    }

    public static function getInfoRoomPresenceChannel($roomID)
    {
        $pusher = self::getConfigPusher();

        $channels = $pusher->get_channels(['filter_by_prefix' => 'presence-']);
        if(count($channels->channels) > 0 && !empty($roomID))
        {
            $channel_name = 'presence-room-'.$roomID;
            $info = $pusher->get_channel_info($channel_name, ['info' => 'user_count']);
            $user_count = $info->user_count;
            return $user_count;
        }else{
            return false;
        }
    }

    public static function getInfoRoomsPresenceChannel()
    {
        $pusher = self::getConfigPusher();

        $channels = $pusher->get_channels(['filter_by_prefix' => 'presence-']);
        if(count($channels->channels) > 0)
        {
            $subscription_counts = [];
            foreach ($channels->channels as $channel => $v) {
            $subscription_counts[$channel] =
                $pusher->get_channel_info(
                $channel, ['info' => 'user_count']
                )->user_count;
            }
            return $subscription_counts;
        }else{
            return [];
        }
    }

    public static function getIdRoomCountUserFromPresenceChannel()
    {
        $rooms = self::getInfoRoomsPresenceChannel();
        $arr = [];
        if(count($rooms) > 0)
        {
            $ar = [];
            foreach($rooms as $key => $countroom)
            {
                $ar['owner_room_id'] = (int)preg_replace('/[^0-9]/', '',$key);
                $ar['count_user'] = $countroom;
                array_push($arr, $ar);
            }
        }
        return $arr;
    }
}
