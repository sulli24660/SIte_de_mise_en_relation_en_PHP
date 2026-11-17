<?php

declare(strict_types=1);

namespace App\Exception;

use App\Validator\ErrorBag;
use Exception;

final class ValidationException extends Exception
{
    public function __construct(
        public readonly ErrorBag $errors,
        public readonly array $old = [],
    ) {
        parent::__construct('Les données envoyées ne sont pas valides.', 422);
    }
}