<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\State\CheckUserForcedLogoutState;
use App\User;
use Closure;

class CheckUserStateChange
{
    /**
     * @var CheckUserForcedLogoutState
     */
    private $checkUserForcedLogoutState;

    /**
     * CheckUserStateChange constructor.
     *
     * @param CheckUserForcedLogoutState $checkUserForcedLogoutState
     */
    public function __construct(CheckUserForcedLogoutState $checkUserForcedLogoutState)
    {
        $this->setCheckUserForcedLogoutState($checkUserForcedLogoutState);
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = auth()->user();

        if (null !== $user) {
            $didStateChange = $this
                ->getCheckUserForcedLogoutState()
                ->setUser($user)
                ->hasUserBeenForcedToLogout();

            if ($didStateChange) {
                redirect('/');
            }
        }

        return $next($request);
    }

    /**
     * @return CheckUserForcedLogoutState
     */
    public function getCheckUserForcedLogoutState(): CheckUserForcedLogoutState
    {
        return $this->checkUserForcedLogoutState;
    }

    /**
     * @param CheckUserForcedLogoutState $checkUserForcedLogoutState
     *
     * @return CheckUserStateChange
     */
    public function setCheckUserForcedLogoutState(CheckUserForcedLogoutState $checkUserForcedLogoutState
    ): self {
        $this->checkUserForcedLogoutState = $checkUserForcedLogoutState;

        return $this;
    }
}
