<?php

declare(strict_types=1);

namespace App\Request;

final class BookRequest extends FormRequest
{
    protected const DONT_FLASH = [];

    public function data(): array
    {
        return [
            'title' => $this->post('title'),
            'author' => $this->post('author'),
            'description' => $this->post('description'),
            'is_available' => $this->post('is_available', '1'),
        ];
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'author' => ['required', 'max:255'],
            'description' => ['max:2000'],
            'is_available' => ['required', 'regex:/^[01]$/D'],
        ];
    }

    protected function messages(): array
    {
        return [
            'is_available.regex' => 'Choisissez une disponibilité dans la liste.',
        ];
    }
}
