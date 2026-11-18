<?php

declare(strict_types=1);

namespace App\Request;

final class UpdateAccountRequest extends RegisterRequest
{
    protected function rules(): array
    {
        $rules = parent::rules();
        // Facultatif : laissé vide, le mot de passe actuel est conservé.
        $rules['password'] = ['min:8', 'maxbytes:72'];

        return $rules;
    }
}
