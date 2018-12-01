<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class AuthControllerTest
 * @package Tests\Feature
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_USERNAME = 'testuser';
    private const TEST_EMAIL    = 'email@test.com';
    private const TEST_PASSWORD = 'password';

    /**
     * @test
     * @group Auth
     */
    public function register()
    {
        $this->post('api/v1/auth/register', [
            'username' => self::TEST_USERNAME,
            'email'    => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', [
            'username'          => self::TEST_USERNAME,
            'email'             => self::TEST_EMAIL,
            'email_verified_at' => null,
        ]);
    }

    /**
     * @test
     * @group Auth
     */
    public function login()
    {
        factory(User::class)->create([
            'username' => self::TEST_USERNAME,
            'email'    => self::TEST_EMAIL,
            'password' => bcrypt(self::TEST_PASSWORD),
        ]);

        /* Login using username */
        $this->post('api/v1/auth/login', [
            'username' => self::TEST_USERNAME,
            'password' => self::TEST_PASSWORD,
        ])->assertStatus(Response::HTTP_OK);

        /* Login using email */
        $this->post('api/v1/auth/login', [
            'email'    => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ])->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     * @group Auth
     */
    public function refresh()
    {
        factory(User::class)->create([
            'username' => self::TEST_USERNAME,
            'email'    => self::TEST_EMAIL,
            'password' => bcrypt(self::TEST_PASSWORD),
        ]);

        $refreshToken = $this->post('api/v1/auth/login', [
            'username' => self::TEST_USERNAME,
            'password' => self::TEST_PASSWORD,
        ])->assertStatus(Response::HTTP_OK)->json('refreshToken');

        $this->get('api/v1/auth/token', [
            'Authorization' => 'Bearer ' . $refreshToken,
        ])->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     * @group Auth
     */
    public function checkIp()
    {
        factory(User::class)->create([
            'username' => self::TEST_USERNAME,
            'email'    => self::TEST_EMAIL,
            'password' => bcrypt(self::TEST_PASSWORD),
        ]);

        /* Call login to set ip */
        $refreshToken = $this->call('POST', 'api/v1/auth/login', [
            'username' => self::TEST_USERNAME,
            'password' => self::TEST_PASSWORD,
        ], [], [], ['REMOTE_ADDR' => '10.1.0.1'])->assertStatus(Response::HTTP_OK)->json('refreshToken');

        /* Call refresh token with different ip to test if server rejects */
        $this->call('GET', 'api/v1/auth/token', [], [], [], $this->transformHeadersToServerVars([
            'REMOTE_ADDR' => '10.1.0.2',
            'Authorization' => 'Bearer ' . $refreshToken,
        ]))->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
