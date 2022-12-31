<?php

namespace Botble\Impersonate\Guard;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;

class SessionGuard extends BaseSessionGuard
{
    public function quietLogin(Authenticatable $user): void
    {
        $this->updateSession($user->getAuthIdentifier());

        $this->setUser($user);
    }

    public function quietLogout(): void
    {
        $this->clearUserDataFromStorage();

        $this->user = null;

        $this->loggedOut = true;
    }
}
