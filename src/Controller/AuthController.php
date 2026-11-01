<?php

declare(strict_types=1);

namespace App\Controller;


use App\Service\DBManager;

final class AuthController
{
    public function __construct(
    ) {
    }

    public function login($post)
    {

        $user = $post['user'];
        $password = $post['password'];

        $hashedPassword = DBManager::getInstance()
            ->getPDO()
            ->query('select password from users where user = :user');

        if(!password_verify($password, $hashedPassword)) {
            abort(403);
        }


        setcookie('user', $user, time() + (86400 * 30), "/");

        return 'ok';
    }

    public function logout()
    {
        session_unset();
    }
}
