<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CreateUserRequest;
use App\Service\View;

class UserController
{
    public function register(CreateUserRequest $request): void
    {
        if ($request->isPost()) {
            $request->validate();
        }

        (new View())->render('users/register');
    }
}
