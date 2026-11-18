<?php

declare(strict_types=1);

namespace App\Request;

class RegisterRequest extends FormRequest
{
    public function data(): array
    {
        return [
            'pseudo' => $this->post('pseudo'),
            'email' => mb_strtolower($this->post('email')),
            'password' => $this->postRaw('password'),
        ];
    }

    protected function rules(): array
    {
        return [
            'pseudo' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/D', 'unique:pseudo'],
            'email' => ['required', 'max:255', 'email', 'unique:email'],
            // bcrypt ignore tout ce qui dépasse 72 octets.
            'password' => ['required', 'min:8', 'maxbytes:72'],
        ];
    }

    protected function messages(): array
    {
        return [
            'pseudo.regex' => 'Le pseudo ne peut contenir que des lettres, des chiffres et des underscores.',
            'pseudo.unique' => 'Ce pseudo est déjà pris.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
        ];
    }
}