<?php

namespace App\Core;

class Response
{
    public function __construct(
        public readonly string $content,
        public readonly int $status = 200,
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);
        echo $this->content;
    }
}
