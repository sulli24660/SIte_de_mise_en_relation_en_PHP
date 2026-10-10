<?php

namespace App\Exceptions;

use Exception;

class TotoInvalidException extends Exception implements SmsNotifiabled
{
    public function __construct(string $namespace) {
        $msg = "Le namespace $namespace n'est pas valide";
        parent::__construct($msg);
    }
}
