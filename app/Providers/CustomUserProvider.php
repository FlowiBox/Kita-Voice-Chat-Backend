<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\ServiceProvider;

class CustomUserProvider implements UserProvider
{

    /**
     * @inheritDoc
     */
    public function retrieveById ( $identifier )
    {
        // TODO: Implement retrieveById() method.
    }

    /**
     * @inheritDoc
     */
    public function retrieveByToken ( $identifier , $token )
    {
        // TODO: Implement retrieveByToken() method.
    }

    /**
     * @inheritDoc
     */
    public function updateRememberToken ( Authenticatable $user , $token )
    {
        // TODO: Implement updateRememberToken() method.
    }

    /**
     * @inheritDoc
     */
    public function retrieveByCredentials ( array $credentials )
    {
        // TODO: Implement retrieveByCredentials() method.
    }

    /**
     * @inheritDoc
     */
    public function validateCredentials ( Authenticatable $user , array $credentials )
    {
        // TODO: Implement validateCredentials() method.
    }
}
