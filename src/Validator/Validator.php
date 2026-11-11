<?php

declare(strict_types=1);

namespace App\Validator;

use InvalidArgumentException;

final class Validator
{
    private const MESSAGES = [
        'required' => 'Ce champ est requis.',
        'min' => 'Ce champ doit contenir au moins %s caractères.',
        'max' => 'Ce champ ne doit pas dépasser %s caractères.',
        'maxbytes' => 'Ce champ ne doit pas dépasser %s caractères.',
        'email' => 'L\'adresse e-mail n\'est pas valide.',
        'regex' => 'Ce champ n\'est pas au bon format.',
    ];

    public static function validate(array $data, array $rules, array $messages = []): ErrorBag
    {
        $errors = new ErrorBag();

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';

            if ($value === '' && !in_array('required', $fieldRules, true)) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                // Limite à 2 morceaux : un motif regex peut contenir des « : ».
                $parts = explode(':', $rule, 2);
                $name = $parts[0];
                $param = $parts[1] ?? '';

                if (!self::isRuleSatisfied($name, $value, $param)) {
                    $message = $messages[$field . '.' . $name] ?? sprintf(self::MESSAGES[$name], $param);
                    $errors->add($field, $message);
                    break;
                }
            }
        }

        return $errors;
    }

    private static function isRuleSatisfied(string $rule, string $value, string $param): bool
    {
        return match ($rule) {
            'required' => $value !== '',
            'min' => mb_strlen($value) >= (int) $param,
            'max' => mb_strlen($value) <= (int) $param,
            'maxbytes' => strlen($value) <= (int) $param,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'regex' => preg_match($param, $value) === 1,
            default => throw new InvalidArgumentException("Règle de validation inconnue : « $rule »."),
        };
    }
}
