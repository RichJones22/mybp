<?php

declare(strict_types=1);

namespace App\Domain\State;

use App\User;

class CheckUserForcedLogoutState
{
    /** @var User */
    private $user;

    /**
     * @param User $user
     *
     * @return CheckUserForcedLogoutState
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return CheckUserForcedLogoutState
     */
    public function forceUserLogout(): self
    {
        $this->setAndSaveUserForcedLogout(true);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUserBeenForcedToLogout(): bool
    {
        /* @var User $user */
//        $user = $this->getUser();

        if (auth()->user()->forced_logout) {
            auth()->logout();
            $this->setAndSaveUserForcedLogout(false);

            return true;  // caller should perform a redirect('/')
        }

        return false;
    }

    /**
     * @return User
     */
    private function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param bool $value
     */
    private function setAndSaveUserForcedLogout(bool $value): void
    {
        /** @var User $user */
        $user = $this->getUser();

        $user->setAttribute('forced_logout', $value);

        $user->save();
    }
}
