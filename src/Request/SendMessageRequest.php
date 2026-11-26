<?php

declare(strict_types=1);

namespace App\Request;

final class SendMessageRequest extends FormRequest
{
    protected const DONT_FLASH = [];

    public function data(): array
    {
        return [
            'content' => $this->post('content'),
        ];
    }

    protected function rules(): array
    {
        return [
            'content' => ['required', 'max:2000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'content.required' => 'Votre message est vide.',
        ];
    }
}
