<?php

declare(strict_types=1);

namespace App\Validator;

use InvalidArgumentException;

/**
 * Valide des données de formulaire à partir de règles déclaratives.
 *
 * Exemple :
 *     $errors = Validator::validate(
 *         ['pseudo' => $pseudo],
 *         ['pseudo' => ['required', 'min:3', 'max:50']],
 *         ['pseudo.min' => 'Le pseudo est trop court.']
 *     );
 *
 * Règles disponibles : required, min:n, max:n, maxbytes:n, email, regex:motif.
 */
final class Validator
{
    /** Messages par défaut ; %s est remplacé par le paramètre de la règle. */
    private const MESSAGES = [
        'required' => 'Ce champ est requis.',
        'min' => 'Ce champ doit contenir au moins %s caractères.',
        'max' => 'Ce champ ne doit pas dépasser %s caractères.',
        'maxbytes' => 'Ce champ ne doit pas dépasser %s caractères.',
        'email' => 'L\'adresse e-mail n\'est pas valide.',
        'regex' => 'Ce champ n\'est pas au bon format.',
    ];

    /**
     * @param array<string, string>       $data     Valeurs à valider, indexées par nom de champ.
     * @param array<string, list<string>> $rules    Règles à appliquer à chaque champ.
     * @param array<string, string>       $messages Messages personnalisés, indexés par « champ.règle ».
     *
     * @return ErrorBag Première erreur de chaque champ invalide ; vide si tout est valide.
     */
    public static function validate(array $data, array $rules, array $messages = []): ErrorBag
    {
        $errors = new ErrorBag();

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';

            // Un champ facultatif laissé vide n'est soumis à aucune autre règle.
            if ($value === '' && !in_array('required', $fieldRules, true)) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                // Limite à 2 morceaux : un motif « regex » peut lui-même contenir des « : ».
                $parts = explode(':', $rule, 2);
                $name = $parts[0];
                $param = $parts[1] ?? '';

                if (!self::passes($name, $value, $param)) {
                    $message = $messages[$field . '.' . $name] ?? sprintf(self::MESSAGES[$name], $param);
                    $errors->add($field, $message);
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * @throws InvalidArgumentException Si la règle est inconnue, pour ne jamais l'ignorer en silence.
     */
    private static function passes(string $rule, string $value, string $param): bool
    {
        return match ($rule) {
            'required' => $value !== '',
            'min' => mb_strlen($value) >= (int) $param,
            'max' => mb_strlen($value) <= (int) $param,
            // Compté en octets : password_hash() (bcrypt) ignore tout ce qui dépasse 72 octets.
            'maxbytes' => strlen($value) <= (int) $param,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'regex' => preg_match($param, $value) === 1,
            default => throw new InvalidArgumentException("Règle de validation inconnue : « $rule »."),
        };
    }
}