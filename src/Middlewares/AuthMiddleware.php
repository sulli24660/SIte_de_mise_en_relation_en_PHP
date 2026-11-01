<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle()
    {
        if (!$pasDecookie) {
          abort(403)  ;
        }
        //
    }
}
