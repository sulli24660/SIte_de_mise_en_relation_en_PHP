<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class NotFoundException extends \Exception
{
    public function __construct(string $message = 'Page Introuvable')
    {
        parent::__construct($message, 404);
    }
}
