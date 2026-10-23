<?php

declare(strict_types=1);

namespace App\Exception;

use App\Validator\ErrorBag;
use Exception;

/**
 * Levée par FormRequest::validate(). Le contrôleur frontal la rattrape :
 * il met les erreurs et la saisie en session, puis redirige vers le formulaire.
 */
class ValidationException extends Exception
{
    /** @param array<string, string> $old saisie à réafficher, sans les champs sensibles */
    public function __construct(
        public readonly ErrorBag $errors,
        public readonly array $old = [],
    ) {
        parent::__construct('Les données envoyées ne sont pas valides.', 422);
    }
}
