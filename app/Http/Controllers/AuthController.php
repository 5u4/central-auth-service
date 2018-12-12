<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

    /** @var MailService $mailService */
    private $mailService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     * @param UserService $userService
     * @param MailService $mailService
     */
    public function __construct(AuthService $authService, UserService $userService, MailService $mailService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->mailService = $mailService;
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

        $this->mailService->sendEmailVerificationEmail($user);

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
            throw new UnauthorizedHttpException(
                $this->authService::CHALLENGE, 'The password does not match the account'
            );
        }

        $accessToken  = $this->authService->getAccessToken();
        $refreshToken = $this->authService->getRefreshToken();

        return UserResource::make(Auth::user())->additional([
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken,
        ])->response();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function verifyEmailRegistration(Request $request): JsonResponse
    {
        $uid          = $request->get('uid');
        $verification = $request->get('verification');

        if (!$uid || !$verification || $this->mailService->verifyEmailVerificationCode($uid, $verification) === false) {
            throw new BadRequestHttpException('Unable to verify your email.');
        }

        $user = User::findOrFail($uid);

        if ($user->email_verified_at) {
            throw new AccessDeniedHttpException('Account has already been verified');
        }

        $user->update([
            'email_verified_at' => time(),
        ]);

        return response()->json();
    }

    /**
     * @param string $uid
     *
     * @return JsonResponse
     */
    public function sendVerificationEmail(string $uid): JsonResponse
    {
        $this->mailService->sendEmailVerificationEmail(User::findOrFail($uid));

        return response()->json();
    }
}
