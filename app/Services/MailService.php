<?php

namespace App\Services;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class MailService
 * @package App\Services
 */
class MailService
{
    private const DEFAULT_CODE_LENGTH = 32;

    /**
     * @param string $uid
     * @param string $verification
     *
     * @return bool
     */
    public function verifyEmailVerificationCode(string $uid, string $verification): bool
    {
        return Redis::get(config('redis.keys.register_verification') . $uid) === $verification;
    }

    /**
     * @param User $user
     */
    public function sendEmailVerificationEmail(User $user)
    {
        if ($user->email_verified_at) {
            throw new AccessDeniedHttpException('Account has already been verified');
        }

        $verification = $this->generateVerificationCode();

        $activationUrl = $this->generateActivationUrl($user->id, $verification);

        Redis::set(
            config('redis.keys.register_verification') . $user->id,
            $verification,
            'EX', config('redis.expire_time.register_verification')
        );

        Mail::to([[
            'email' => $user->email,
            'name'  => $user->username,
        ]])->send(new VerifyEmail($user->username, $activationUrl));
    }

    /**
     * @return string
     */
    private function generateVerificationCode(): string
    {
        return bin2hex(random_bytes(self::DEFAULT_CODE_LENGTH));
    }

    /**
     * @param string $userId
     * @param string $verification
     *
     * @return string
     */
    private function generateActivationUrl(string $userId, string $verification): string
    {
        // TODO: Redirect to front end verification url instead of back end

        return action('AuthController@verifyEmailRegistration', [
            'uid' => $userId,
            'verification' => $verification,
        ]);
    }
}
