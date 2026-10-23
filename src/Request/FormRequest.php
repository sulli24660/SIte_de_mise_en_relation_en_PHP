<?php

declare(strict_types=1);

namespace App\Request;

use App\Exception\ValidationException;
use App\Validator\ErrorBag;
use App\Validator\Validator;

/**
 * Requête de formulaire : chaque sous-classe déclare ses données et ses règles,
 * le Validator injecté les applique.
 */
abstract class FormRequest extends Request
{
    /** Champs jamais renvoyés à la vue dans $old. */
    protected const DONT_FLASH = ['password', 'password_confirmation'];

    public function __construct(
        private readonly Validator $validator,
        ?array $query = null,
        ?array $body = null,
        ?string $method = null,
    ) {
        parent::__construct($query, $body, $method);
    }

    /**
     * Données normalisées du formulaire, celles qui sont validées.
     *
     * @return array<string, string>
     */
    abstract public function data(): array;

    /**
     * @return array<string, list<string>>
     */
    abstract protected function rules(): array;

    /**
     * Surcharges de messages par "champ.règle".
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * @return array<string, string> les données validées
     * @throws ValidationException si une règle échoue
     */
    public function validate(): array
    {
        $data = $this->data();
        $errors = $this->validator->validate($data, $this->rules(), $this->messages());

        if ($errors !== []) {
            throw new ValidationException(
                new ErrorBag($errors),
                array_diff_key($data, array_flip(static::DONT_FLASH)),
            );
        }

        return $data;
    }
}
