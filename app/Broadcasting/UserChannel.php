<?php

namespace App\Broadcasting;

use App\Models\User;
 
class UserChannel
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $id): array|bool
    {
        return $user->id === $id;
    }
}