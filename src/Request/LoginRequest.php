<?php

declare(strict_types=1);

namespace App\Request;

final class LoginRequest extends FormRequest
{
    public function data(): array
    {
        return [
            'email' => mb_strtolower($this->post('email')),
            'password' => $this->postRaw('password'),
        ];
    }

    protected function rules(): array
    {
        return [
            'email' => ['required'],
            'password' => ['required'],
        ];
    }
}