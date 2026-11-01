<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\UserManager;
use App\Service\CsrfTokenManager;
use App\Service\View;
use App\Validator\ErrorBag;
use App\Validator\Validator;

/**
 * Gère les pages liées aux utilisateurs (inscription, puis connexion).
 */
final class UserController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly View $view,
        private readonly CsrfTokenManager $csrfTokenManager,
    ) {}

    /**
     * Affiche le formulaire d'inscription et traite sa soumission.
     *
     * En cas de succès, l'utilisateur est enregistré puis redirigé
     * (principe Post/Redirect/Get : un rafraîchissement de la page
     * ne renvoie pas le formulaire).
     */
    public function register(): void
    {
        $errors = null;
        $pseudo = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = trim($this->getPostValue('pseudo'));
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');

            // Contrôle CSRF en premier : une requête forgée n'est jamais traitée.
            if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
                $errors = new ErrorBag();
                $errors->add('form', 'Votre session a expiré. Merci de renvoyer le formulaire.');
            } else {
                $errors = $this->validateRegistration($pseudo, $email, $password);

                if ($errors->isEmpty()) {
                    $user = new User();
                    $user->setPseudo($pseudo);
                    $user->setEmail($email);
                    // Le mot de passe n'est jamais stocké en clair.
                    $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

                    $this->userManager->create($user);

                    header('Location: index.php?action=books');
                    exit;
                }
            }
        }

        // Le mot de passe n'est volontairement jamais renvoyé à la vue.
        $this->view->render('users/register', [
            'errors' => $errors,
            'pseudo' => $pseudo,
            'email' => $email,
            'csrfToken' => $this->csrfTokenManager->getToken(),
        ]);
    }

    /**
     * Valide les données d'inscription : format des champs,
     * puis unicité du pseudo et de l'e-mail en base.
     *
     * @return ErrorBag Erreurs trouvées (vide si tout est valide).
     */
    private function validateRegistration(string $pseudo, string $email, string $password): ErrorBag
    {
        $errors = Validator::validate(
            [
                'pseudo' => $pseudo,
                'email' => $email,
                'password' => $password,
            ],
            [
                'pseudo' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/D'],
                'email' => ['required', 'max:255', 'email'],
                // bcrypt ignore tout ce qui dépasse 72 octets : on le refuse explicitement.
                'password' => ['required', 'min:8', 'maxbytes:72'],
            ],
            [
                'pseudo.regex' => 'Le pseudo ne peut contenir que des lettres, des chiffres et des underscores.',
            ]
        );

        // Requêtes en base uniquement si le format est déjà valide.
        if (!$errors->has('pseudo') && $this->userManager->pseudoExists($pseudo)) {
            $errors->add('pseudo', 'Ce pseudo est déjà pris.');
        }

        if (!$errors->has('email') && $this->userManager->emailExists($email)) {
            $errors->add('email', 'Cette adresse e-mail est déjà utilisée.');
        }

        return $errors;
    }

    /**
     * Lit une valeur de $_POST en garantissant une chaîne de caractères.
     *
     * Un champ absent, ou envoyé sous forme de tableau (name="pseudo[]"),
     * est remplacé par une chaîne vide.
     */
    private function getPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}