<?php

namespace App\Http\Resources\Api\V2;

use App\Http\Resources\Api\V1\MiniUserResource;
use App\Models\GiftLog;
use App\Models\Pk;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterRoomCollection extends JsonResource
{

    public $userId;

    public function __construct($resource, $userId)
    {
        parent::__construct($resource);
        $this->userId = $userId;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $pks     = $this->getRoomTwoLastPk($this->id);
        $topUser = $this->getTopUser($this->uid);
        return [
            "id"                  => $this->id,
            "room_id_num"         => $this->numid,
            "mode"                => $this->mode,
            "owner_id"            => $this->uid,
            "room_status"         => $this->room_status,
            "room_name"           => $this->room_name,
            "room_cover"          => $this->room_cover,
            "name"                => @$this->roomCategory->name ?? '',
            "room_intro"          => $this->room_intro,
            "room_pass"           => $this->room_pass,
            "room_type"           => $this->room_type ?? '',
            "hot"                 => '',
            "room_background"     => $this->room_background,
            "microphone"          => $this->microphone,
            "room_welcome"        => $this->room_welcome,
            "session"             => $this->session,
            "uuid"                => $this->owner->uuid,
            "room_family"         => [
                'family_id'    => @$this->family->id ?? '',
                'family_name'  => @$this->family->name ?? '',
                'family_level' => @$this->family->level ?? [],
            ],
            "giftPrice"           => $this->session ?: '',
            "pk"                  => $pks[0]->toArray(),
            'top_user'            => $topUser ? (new MiniUserResource($topUser)) : new \stdClass(),
            'admins'              => explode(',', $this->room_admin ?? ''),
            'owner_sound'         => $this->getOwnerSound($this->uid, $this->room_sound) ? 2 : 1,
            'ban_users'           => $this->getBans($this->room_speak ?? ''),
            'owner_name'          => @$this->owner->name ?? '',
            'owner_avatar'        => @$this->owner->profile->avatar ?? '',
            'room_visitors_count' => $this->getRoomVisitorCount(@$this->room_visitor ?? ''),
            'microphones'         => $this->getMicrophones($this->microphone)
        ];
    }

    private function getRoomTwoLastPk(int $roomId)
    {
        return Pk::query()
                 ->where('room_id', $roomId)
                 ->orderByDesc('created_at')
                 ->limit(2)
                 ->get();
    }

    private function getTopUser(int $roomOwner)
    {
        $gl = GiftLog::query()
                     ->selectRaw('sender_id, SUM(giftNum * giftPrice) AS total')
                     ->where('roomowner_id', $roomOwner)
                     ->groupBy('sender_id')
                     ->orderByDesc('total')
                     ->first();
        if ($gl) {

            $t_user = User::query()->find($gl->sender_id);
        } else {
            $t_user = null;
        }

        return $t_user;
    }

    public function getOwnerSound($ownerId, $roomSound)
    {
        $roomSound = explode(',', $roomSound);
        return in_array($ownerId, $roomSound);
    }

    private function getBans($roomSpeak)
    {
        $roomSpeak = trim($roomSpeak);
        if ($roomSpeak == '') return [];
        $bans      = [];
        $uid_black = explode(',', trim($roomSpeak));
        foreach ($uid_black as $b) {
            $u      = explode('#', trim($b));
            $bans[] = $u[0];
        }
        return $bans;
    }

    private function getRoomVisitorCount($roomVisitors)
    {
        $roomVisitors = trim($roomVisitors);
        if ($roomVisitors == '') return 1;
        return count(explode(',', $roomVisitors)) + 1;

    }

    private function getMicrophones($microphones)
    {
        $microphones = trim($microphones);
        if ($microphones == '') return [];
        $microphones = explode(',', $microphones);
        $arr         = ['0', '-1', '-2'];

        $usersIds = array_diff($microphones, $arr);

        if (count($usersIds) > 0) {
            //get all users with ids
            $users = User::withoutAppends()->whereIn('id', $usersIds)->select(['id', 'name'])->get();
        }

        for ($i = 0, $j = 0; $i < count($microphones); $i++) {
            $mic = $microphones[$i];
            if ($mic == '0') {
                $microphones[$i] = 'empty';
            } elseif ($mic == '-1') {
                $microphones[$i] = 'locked';
            } elseif ($mic == '-2') {
                $microphones[$i] = 'muted';
            } else {
                $microphones[$i] = $users[$j]->setAppends([])->toArray();
            }
        }
        return $microphones;

    }

    private function getUserType($roomAdmin, $roomJudge)
    {
        $userType = 5;
        [$isAdminInRoom, $roomAdmin] = $this->getAdminAndType(trim($roomAdmin));
        //        [$isUserIsJudge, $roomJudge] = $this->getAdminAndType(trim($roomJudge));

        if ($isAdminInRoom) $userType = 2;
        //        if($isUserIsJudge) $userType = 4;

        return [$userType, $roomAdmin];
    }

    private function getAdminAndType($roomAdmins)
    {
        $isAdminInRoom = false;
        $roomAdmin     = explode(',', $roomAdmins ?? '');

        if (in_array((string)$this->userId, $roomAdmin)) $isAdminInRoom = true;
        return [$isAdminInRoom, $roomAdmin];
    }

    private function getJudgeAndType($roomJudge)
    {
        $isUserIsJudge = false;
        $roomJudge     = explode(',', $roomJudge ?? '');

        if (in_array((string)$this->userId, $roomJudge)) $isUserIsJudge = true;
        return [$isUserIsJudge, $roomJudge];
    }
}
