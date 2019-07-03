<?php

namespace App\Services;

use App\Models\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * Create an user
     *
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public function createUser(string $username, string $email, string $password): User
    {
        return User::create([
            'username' => $username,
            'email'    => $email,
            'password' => bcrypt($password),
        ]);
    }
}
