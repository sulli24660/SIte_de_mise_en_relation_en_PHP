<?php

declare(strict_types=1);

namespace App\Request;

final class DeleteBookRequest extends FormRequest
{
    public function data(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [];
    }
}
