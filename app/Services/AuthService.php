<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    public const CHALLENGE = 'jwt-auth';

    private const JWT_ALGORITHM = 'HS256';

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->makeToken(config('jwt.ttl.access_token'));
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->makeToken(config('jwt.ttl.refresh_token'));
    }

    /**
     * Decode a token and return the corresponding user
     *
     * @param null|string $token
     *
     * @return User
     */
    public function verifyToken(?string $token): User
    {
        if ($token === null) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Token is not specified');
        }

        $decoded = JWT::decode($token, config('jwt.key'), [self::JWT_ALGORITHM]);

        return User::find($decoded->uid);
    }

    /**
     * Make a token
     *
     * @param int $ttl
     *
     * @return string
     */
    private function makeToken(int $ttl): string
    {
        $uid = Auth::id();

        if ($uid === null) {
            throw new UnauthorizedHttpException(self::CHALLENGE, 'Not authorized');
        }

        $issueAt = time();
        $expiredAt = $issueAt + $ttl;

        return JWT::encode([
            'iat' => $issueAt,
            'exp' => $expiredAt,
            'uid' => $uid,
        ], config('jwt.key'), self::JWT_ALGORITHM);
    }
}
