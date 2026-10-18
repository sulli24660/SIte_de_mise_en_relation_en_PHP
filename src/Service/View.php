<?php

declare(strict_types=1);

namespace App\Service;

class View
{
    public function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}