<?php

declare(strict_types=1);

namespace App\Validator;

use InvalidArgumentException;
use LogicException;
use PDO;

/**
 * Valide des données selon des règles déclaratives :
 * ['pseudo' => ['required', 'min:3', 'max:50', 'regex:/^\w+$/D', 'unique:users,pseudo']].
 * Règles : required, min:n, max:n, email, regex:motif, unique:table,colonne.
 */
final class Validator
{
    private const MESSAGES = [
        'required' => 'Ce champ est requis.',
        'min' => 'Ce champ doit contenir au moins %s caractères.',
        'max' => 'Ce champ ne doit pas dépasser %s caractères.',
        'email' => 'L\'adresse e-mail n\'est pas valide.',
        'regex' => 'Ce champ n\'est pas au bon format.',
        'unique' => 'Cette valeur est déjà utilisée.',
    ];

    private const IDENTIFIER = '/^[a-zA-Z_][a-zA-Z0-9_]*$/D';

    /** $pdo n'est requis que si une règle « unique » est utilisée. */
    public function __construct(private readonly ?PDO $pdo = null)
    {
    }

    /**
     * @param array<string, string> $data
     * @param array<string, list<string>> $rules
     * @param array<string, string> $messages surcharges par "champ.règle", ex. ['pseudo.regex' => '...']
     * @return array<string, string> première erreur de chaque champ invalide ; vide si tout est valide
     */
    public function validate(array $data, array $rules, array $messages = []): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';

            // Un champ facultatif laissé vide n'est pas soumis aux autres règles.
            if ($value === '' && !in_array('required', $fieldRules, true)) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, '');

                if (! $this->passes($name, $value, $param)) {
                    $errors[$field] = $messages["$field.$name"] ?? sprintf(self::MESSAGES[$name], $param);
                    break;
                }
            }
        }

        return $errors;
    }

    private function passes(string $rule, string $value, string $param): bool
    {
        // Longueurs en octets (strlen) : bcrypt ignore tout ce qui dépasse 72 octets.
        return match ($rule) {
            'required' => $value !== '',
            'min' => strlen($value) >= (int) $param,
            'max' => strlen($value) <= (int) $param,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'regex' => preg_match($param, $value) === 1,
            'unique' => !$this->exists($param, $value),
            default => throw new InvalidArgumentException("Règle de validation inconnue : « $rule »."),
        };
    }

    private function exists(string $param, string $value): bool
    {
        if ($this->pdo === null) {
            throw new LogicException('La règle « unique » nécessite un PDO dans le constructeur du Validator.');
        }

        [$table, $column] = array_pad(explode(',', $param, 2), 2, '');

        // Table et colonne ne peuvent pas être des paramètres préparés : on les restreint à des identifiants.
        if (!preg_match(self::IDENTIFIER, $table) || !preg_match(self::IDENTIFIER, $column)) {
            throw new InvalidArgumentException("Règle « unique:$param » invalide : attendu unique:table,colonne.");
        }

        $stmt = $this->pdo
            ->prepare(
                sprintf(
                    'SELECT 1 FROM `%s` WHERE `%s` = :value LIMIT 1',
                    $table,
                    $column
                ));
        $stmt->execute(['value' => $value]);

        return $stmt->fetch() !== false;
    }
}
