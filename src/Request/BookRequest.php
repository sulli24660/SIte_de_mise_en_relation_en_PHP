<?php

declare(strict_types=1);

namespace App\Request;

use App\Service\CsrfTokenManager;
use App\Service\ImageUploader;
use App\Validator\ErrorBag;
use App\Validator\Validator;

final class BookRequest extends FormRequest
{
    protected const DONT_FLASH = [];

    public function __construct(
        Validator $validator,
        CsrfTokenManager $csrfTokenManager,
        private readonly ImageUploader $imageUploader,
    ) {
        parent::__construct($validator, $csrfTokenManager);
    }

    public function data(): array
    {
        return [
            'title' => $this->post('title'),
            'author' => $this->post('author'),
            'description' => $this->post('description'),
            'is_available' => $this->post('is_available', '1'),
        ];
    }

    public function image(): ?array
    {
        return $this->file('image');
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

    protected function validateFiles(ErrorBag $errors): void
    {
        $image = $this->image();
        $error = $image === null ? null : $this->imageUploader->getValidationError($image);

        if ($error !== null) {
            $errors->add('image', $error);
        }
    }
}
