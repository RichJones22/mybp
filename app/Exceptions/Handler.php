<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Nova\Exceptions\AuthenticationException as NovaAuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     *
     * @throws AuthenticationException
     */
    public function render($request, Exception $exception)
    {
        /*
         * - trap for NovaAuthenticationException
         * - log the auth() user out
         * - flush the session data.
         * - throw a new AuthenticationException()
         *   - this will cause the 'login' route to be called and not
         *     the nova.login route.
         */
        if ($exception instanceof NovaAuthenticationException) {
            auth()->logout();
            $request->session()->flush();

            throw new AuthenticationException();
        }

        return parent::render($request, $exception);
    }
}
