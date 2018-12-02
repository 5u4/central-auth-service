<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
class Authenticate extends Middleware
{
    /** @var AuthService $authService */
    private $authService;

    /**
     * Authenticate constructor.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @param AuthService $authService
     */
    public function __construct(\Illuminate\Contracts\Auth\Factory $auth, AuthService $authService)
    {
        parent::__construct($auth);

        $this->authService = $authService;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param mixed ...$guards
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $user = $this->authService->verifyToken($request->bearerToken());

        if ($this->authService->isLoggedIp($user->id, $request->ip()) === false) {
            throw new AccessDeniedHttpException('User IP does not match with the login IP. Please login again');
        }

        Auth::setUser($user);

        return $next($request);
    }
}
