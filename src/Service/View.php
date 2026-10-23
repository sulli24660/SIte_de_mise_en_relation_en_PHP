<?php

declare(strict_types=1);

namespace App\Service;

use App\Validator\ErrorBag;

class View
{
    /**
     * Toutes les vues reçoivent $errors (ErrorBag) et $old (saisie précédente),
     * remplis après un échec de validation ; le contrôleur peut les surcharger.
     */
    public function render(string $view, array $data = []): void
    {
        $data += [
            'errors' => new ErrorBag(Flash::pull('errors', [])),
            'old' => Flash::pull('old', []),
        ];

        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}
