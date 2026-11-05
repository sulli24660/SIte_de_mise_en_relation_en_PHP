<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\UserManager;
use App\Service\AuthService;
use App\Service\CsrfTokenManager;
use App\Service\View;
use App\Validator\ErrorBag;
use App\Validator\Validator;

/**
 * Gère les pages liées aux utilisateurs (inscription, connexion, déconnexion).
 *
 * Le contrôleur traite les formulaires ; la gestion de la session
 * d'authentification est déléguée à l'AuthService.
 */
final class UserController
{
    /**
     * Hash bcrypt d'un mot de passe aléatoire, qui ne correspond à aucun compte.
     *
     * Quand l'e-mail saisi est inconnu, on vérifie quand même le mot de passe
     * contre ce hash : la réponse prend alors le même temps qu'avec un e-mail
     * existant, et un attaquant ne peut pas deviner quels e-mails sont inscrits.
     */
    private const DUMMY_PASSWORD_HASH = '$2y$10$fLAenOmNqh9NCbWm2ESTAuGgSPqH.AWmHjiP4IlZ1vQOH9Lhc2Uhq';

    public function __construct(
        private readonly UserManager $userManager,
        private readonly View $view,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
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
        // Le jeton CSRF est fourni automatiquement par la View.
        $this->view->render('users/register', [
            'errors' => $errors,
            'pseudo' => $pseudo,
            'email' => $email,
        ]);
    }

    /**
     * Affiche le formulaire de connexion et traite sa soumission.
     *
     * En cas de succès, l'utilisateur est connecté via l'AuthService
     * puis redirigé.
     */
    public function login(): void
    {
        $errors = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');
            $errors = new ErrorBag();

            if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
                $errors->add('form', 'Votre session a expiré. Merci de renvoyer le formulaire.');
            } else {
                $user = $this->userManager->findByEmail($email);

                // password_verify() est appelé dans tous les cas (hash factice si
                // l'e-mail est inconnu) pour que le temps de réponse soit identique.
                $passwordHash = $user?->getPassword() ?? self::DUMMY_PASSWORD_HASH;
                $isPasswordValid = password_verify($password, $passwordHash);

                if ($user === null || !$isPasswordValid) {
                    // Message volontairement identique dans les deux cas :
                    // on ne révèle jamais si l'e-mail possède un compte.
                    $errors->add('form', 'Identifiants incorrects.');
                } else {
                    $this->authService->login($user->getId());
                    // Nouveau jeton CSRF : celui d'avant la connexion devient inutilisable.
                    $this->csrfTokenManager->regenerate();

                    header('Location: index.php?action=books');
                    exit;
                }
            }
        }

        // L'e-mail est réaffiché pour le confort de l'utilisateur, jamais le mot de passe.
        $this->view->render('users/login', [
            'errors' => $errors,
            'email' => $email,
        ]);
    }

    /**
     * Déconnecte l'utilisateur via l'AuthService.
     *
     * N'accepte qu'une requête POST avec un jeton CSRF valide : un lien
     * ou une image piégée sur un autre site ne peut pas déconnecter l'utilisateur.
     */
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && $this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)
        ) {
            $this->authService->logout();
        }

        // Dans tous les cas, retour à la liste des livres. Une requête non
        // légitime est simplement ignorée, sans rien modifier.
        header('Location: index.php?action=books');
        exit;
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