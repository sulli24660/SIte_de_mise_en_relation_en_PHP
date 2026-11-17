<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\AbstractManager;
use InvalidArgumentException;
use LogicException;

final class Validator
{
    private const MESSAGES = [
        'required' => 'Ce champ est requis.',
        'min' => 'Ce champ doit contenir au moins %s caractères.',
        'max' => 'Ce champ ne doit pas dépasser %s caractères.',
        'maxbytes' => 'Ce champ ne doit pas dépasser %s caractères.',
        'email' => 'L\'adresse e-mail n\'est pas valide.',
        'regex' => 'Ce champ n\'est pas au bon format.',
        'unique' => 'Cette valeur est déjà utilisée.',
    ];

    public function __construct(private readonly ?AbstractManager $manager = null)
    {
    }

    public function validate(array $data, array $rules, array $messages = []): ErrorBag
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

                if (!$this->isRuleSatisfied($name, $value, $param)) {
                    $message = $messages[$field . '.' . $name] ?? sprintf(self::MESSAGES[$name], $param);
                    $errors->add($field, $message);
                    break;
                }
            }
        }

        return $errors;
    }

    private function isRuleSatisfied(string $rule, string $value, string $param): bool
    {
        return match ($rule) {
            'required' => $value !== '',
            'min' => mb_strlen($value) >= (int) $param,
            'max' => mb_strlen($value) <= (int) $param,
            'maxbytes' => strlen($value) <= (int) $param,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'regex' => preg_match($param, $value) === 1,
            'unique' => !$this->getManager()->exists($param, $value),
            default => throw new InvalidArgumentException("Règle de validation inconnue : « $rule »."),
        };
    }

    private function getManager(): AbstractManager
    {
        if ($this->manager === null) {
            throw new LogicException('La règle « unique » nécessite un manager dans le constructeur du Validator.');
        }

        return $this->manager;
    }
}
