<?php

declare(strict_types=1);

namespace App\Service;

use finfo;
use RuntimeException;

final class ImageUploader
{
    private const MAX_SIZE_IN_BYTES = 2 * 1024 * 1024;

    // L'extension est déduite du contenu réel du fichier, jamais de son nom.
    private const EXTENSIONS_BY_MIME_TYPE = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private readonly string $directory)
    {
    }

    public function getValidationError(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return 'L\'envoi de l\'image a échoué. Elle ne doit pas dépasser 2 Mo.';
        }

        if (($file['size'] ?? PHP_INT_MAX) > self::MAX_SIZE_IN_BYTES) {
            return 'L\'image ne doit pas dépasser 2 Mo.';
        }

        if ($this->detectExtension($file['tmp_name']) === null || getimagesize($file['tmp_name']) === false) {
            return 'Formats acceptés : JPG, PNG ou WebP.';
        }

        return null;
    }

    public function store(array $file): string
    {
        $extension = $this->detectExtension($file['tmp_name'])
            ?? throw new RuntimeException('Format d\'image non autorisé.');

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $this->directory . '/' . $filename)) {
            throw new RuntimeException('Impossible d\'enregistrer l\'image.');
        }

        return $filename;
    }

    public function delete(?string $filename): void
    {
        if ($filename === null) {
            return;
        }

        // basename() empêche de sortir du dossier avec un nom du type « ../../config.php ».
        $path = $this->directory . '/' . basename($filename);

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function detectExtension(string $path): ?string
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        return self::EXTENSIONS_BY_MIME_TYPE[$mimeType] ?? null;
    }
}
