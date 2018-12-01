<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /** @var AuthService $authService */
    private $authService;

    /** @var UserService $userService */
    private $userService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     * @param UserService $userService
     */
    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        $accessToken = $this->authService->getAccessToken();

        return UserResource::make(Auth::user())->additional([
            'accessToken'  => $accessToken,
        ])->response();
    }

    /**
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->username, $request->email, $request->password);

        return UserResource::make($user)->response();
    }

    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->all()) === false) {
            throw new UnauthorizedHttpException($this->authService::CHALLENGE, 'Invalid credentials.');
        }

        $this->authService->setUserIP(Auth::id(), $request->ip());

        $accessToken  = $this->authService->getAccessToken();
        $refreshToken = $this->authService->getRefreshToken();

        return UserResource::make(Auth::user())->additional([
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken,
        ])->response();
    }
}
