<?php

declare(strict_types=1);

namespace App\Request;

use App\Exception\ValidationException;
use App\Service\CsrfTokenManager;
use App\Validator\ErrorBag;
use App\Validator\Validator;

abstract class FormRequest extends Request
{
    protected const DONT_FLASH = ['password'];

    public function __construct(
        private readonly Validator $validator,
        private readonly CsrfTokenManager $csrfTokenManager,
        ?array $query = null,
        ?array $body = null,
        ?string $method = null,
    ) {
        parent::__construct($query, $body, $method);
    }

    abstract public function data(): array;

    abstract protected function rules(): array;

    protected function messages(): array
    {
        return [];
    }

    protected function validateFiles(ErrorBag $errors): void
    {
    }

    public function validate(): array
    {
        $data = $this->data();

        if ($this->hasValidCsrfToken()) {
            $errors = $this->validator->validate($data, $this->rules(), $this->messages());
            $this->validateFiles($errors);
        } else {
            $errors = new ErrorBag(['form' => 'Votre session a expiré. Merci de renvoyer le formulaire.']);
        }

        if (!$errors->isEmpty()) {
            throw new ValidationException($errors, array_diff_key($data, array_flip(static::DONT_FLASH)));
        }

        return $data;
    }

    private function hasValidCsrfToken(): bool
    {
        return $this->csrfTokenManager->isValid($this->post('csrf_token'));
    }
}
