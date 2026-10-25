# TomTroc — Plateforme d'échange de livres

Projet 4 de la formation **Développeur Full Stack** (OpenClassrooms).

TomTroc est une association qui encourage le partage de livres entre particuliers. Ce site permet à ses membres de proposer leurs livres à l'échange, de consulter ceux des autres membres et de communiquer entre eux grâce à une messagerie interne.

> **État du projet : en cours de développement.** Voir la section [Avancement](#avancement).

---

## Sommaire

1. [Contraintes du projet](#contraintes-du-projet)
2. [Avancement](#avancement)
3. [Installation](#installation)
4. [Architecture](#architecture)
5. [Base de données](#base-de-données)
6. [Choix techniques](#choix-techniques)
7. [Sécurité](#sécurité)
8. [Conventions](#conventions)
9. [Pistes d'amélioration](#pistes-damélioration)

---

## Contraintes du projet

- **PHP, HTML et CSS uniquement**, sans aucune librairie ni framework tiers.
- Architecture **MVC** et **programmation orientée objet**.
- Versionnage avec **Git** et **GitHub**.
- Respect de la maquette fournie par le designer.

---

## Avancement

| Fonctionnalité | État |
|---|---|
| Base de données (utilisateurs, livres, messages) | ✅ Terminé |
| Architecture MVC (routeur, contrôleurs, modèles, vues, layout) | ✅ Terminé |
| Gestion centralisée des erreurs (pages 404 / 500) | ✅ Terminé |
| Liste des livres à l'échange | ✅ Terminé |
| Détail d'un livre | ✅ Terminé |
| Inscription : formulaire et validation des données | ✅ Terminé |
| Inscription : enregistrement de l'utilisateur | 🚧 En cours |
| Connexion / déconnexion | ⏳ À faire |
| Mon compte (profil, gestion de ses livres) | ⏳ À faire |
| Ajout, modification et suppression d'un livre | ⏳ À faire |
| Profil public d'un membre | ⏳ À faire |
| Page d'accueil et recherche | ⏳ À faire |
| Messagerie | ⏳ À faire |
| Intégration CSS de la maquette | ⏳ À faire |

---

## Installation

### Prérequis

- **PHP 8.2** ou supérieur, avec l'extension `pdo_mysql`
- **MySQL** ou **MariaDB** (par exemple via XAMPP)
- **Git**

### Étapes

**1. Cloner le dépôt**

```bash
git clone https://github.com/sulli24660/SIte_de_mise_en_relation_en_PHP.git
cd SIte_de_mise_en_relation_en_PHP
```

**2. Créer la base de données**

Créer une base nommée `tomtroc` (interclassement `utf8mb4_unicode_ci`), puis importer le fichier `sql/tomtroc.sql`, par exemple depuis l'onglet **Importer** de phpMyAdmin.

**3. Configurer la connexion**

Copier le fichier de configuration d'exemple, puis renseigner ses propres identifiants :

```bash
cp config/config.example.php config/config.php
```

Le fichier `config/config.php` est ignoré par Git : les identifiants ne sont jamais publiés.

> Sur macOS avec PHP installé via Homebrew et MySQL via XAMPP, utiliser `127.0.0.1` plutôt que `localhost` comme hôte.

**4. Lancer le serveur**

Depuis la racine du projet :

```bash
php -S localhost:8000 -t public
```

Le site est alors accessible à l'adresse **http://localhost:8000**.

L'option `-t public` est indispensable : seul le dossier `public/` doit être exposé au navigateur.

---

## Architecture

Le projet suit le modèle **MVC** (Modèle – Vue – Contrôleur). Toutes les requêtes passent par un **point d'entrée unique**, `public/index.php`.

### Arborescence

```
├── config/
│   ├── autoload.php            Chargement automatique des classes
│   ├── config.example.php      Modèle de configuration (versionné)
│   └── config.php              Identifiants de la base (ignoré par Git)
├── public/                     Seul dossier accessible depuis le navigateur
│   └── index.php               Point d'entrée unique
├── sql/
│   └── tomtroc.sql             Script de création de la base
├── src/
│   ├── Controller/             Contrôleurs : coordonnent modèles et vues
│   ├── Exception/              Exceptions personnalisées
│   ├── Model/                  Entités (Book, User) et managers (requêtes SQL)
│   ├── Service/                Outils techniques (Router, DBManager, View)
│   └── Validator/              Validation des formulaires (Validator, ErrorBag)
└── views/                      Gabarits d'affichage (HTML + PHP)
    ├── layout.php              Squelette commun à toutes les pages
    └── partials/               En-tête et pied de page
```

### Cycle d'une requête

Exemple : affichage du détail d'un livre (`index.php?action=book&id=3`).

```
Navigateur
  → public/index.php              charge la configuration et l'autoloader
  → Router::dispatch()            associe l'action « book » au bon contrôleur
  → BookController::show(3)       demande le livre au modèle
  → BookManager::findById(3)      exécute la requête SQL et renvoie un objet Book
  → View::render()                insère la vue books/show.php dans le layout
  → Page HTML envoyée au navigateur
```

En cas d'erreur, une exception remonte jusqu'à `public/index.php`, qui affiche une page d'erreur adaptée (404 ou 500).

---

## Base de données

Trois tables, au format **InnoDB** et encodées en **utf8mb4** :

| Table | Rôle | Colonnes principales |
|---|---|---|
| `users` | Membres du site | `id`, `pseudo` *(unique)*, `email` *(unique)*, `password` *(haché)*, `avatar`, `created_at` |
| `books` | Livres proposés à l'échange | `id`, `user_id`, `title`, `author`, `image`, `description`, `is_available`, `created_at` |
| `messages` | Messagerie interne | `id`, `sender_id`, `receiver_id`, `content`, `is_read`, `created_at` |

### Relations

- Un **utilisateur** possède plusieurs **livres** (`books.user_id` → `users.id`).
- Un **utilisateur** envoie et reçoit plusieurs **messages** (`messages.sender_id` et `messages.receiver_id` → `users.id`).

Les clés étrangères utilisent `ON DELETE CASCADE` : la suppression d'un compte supprime ses livres et ses messages, sans laisser de données orphelines.

Les valeurs calculables ne sont pas stockées : par exemple, le nombre de livres d'un membre est obtenu par une requête, et non enregistré dans une colonne.

---

## Choix techniques

### Autoloader maison

La contrainte « aucune librairie tierce » exclut Composer. Le fichier `config/autoload.php` enregistre donc une fonction avec `spl_autoload_register()`, qui applique la règle **namespace = dossier** (sur le principe de la norme PSR-4) : la classe `App\Model\Book` est chargée depuis `src/Model/Book.php`.

### Typage strict

Chaque fichier PHP commence par `declare(strict_types=1);`. PHP refuse ainsi les conversions de type implicites, ce qui fait apparaître immédiatement les erreurs au lieu de les masquer.

### Routeur à liste fermée

Le `Router` associe chaque action à un contrôleur dans un `switch`. Seules les actions explicitement prévues peuvent être appelées ; toute autre action déclenche une erreur 404. Aucun nom de classe ou de méthode n'est construit à partir de l'URL.

### Connexion unique à la base

`DBManager` applique le patron **Singleton** : la connexion PDO n'est ouverte qu'une seule fois par requête, quel que soit le nombre de managers qui l'utilisent.

### Entités et managers

Le modèle est séparé en deux types de classes :

- les **entités** (`Book`, `User`) représentent une ligne de la base, avec des propriétés privées et des accesseurs (encapsulation) ;
- les **managers** (`BookManager`, `UserManager`) contiennent les requêtes SQL et transforment les résultats en entités.

### Rendu des vues

La classe `View` insère chaque vue dans un **layout** commun (en-tête, contenu, pied de page). Elle est utilisée par tous les contrôleurs, ainsi que par la page d'erreur.

### Validation déclarative

Les formulaires sont validés par la classe `Validator`, à partir de règles décrites dans un tableau :

```php
$errors = Validator::validate(
    ['pseudo' => $pseudo],
    ['pseudo' => ['required', 'min:3', 'max:50']]
);
```

Règles disponibles : `required`, `min`, `max`, `maxbytes`, `email`, `regex`. Les erreurs sont regroupées dans un objet `ErrorBag` (une erreur par champ au maximum), transmis à la vue pour être affiché sous chaque champ. Une règle inconnue lève une exception, afin de ne jamais être ignorée silencieusement.

Les vérifications qui dépendent de la base de données (pseudo ou e-mail déjà utilisé) restent dans le contrôleur, car elles dépendent du contexte (inscription ou modification du profil).

### Gestion centralisée des erreurs

Toutes les erreurs remontent jusqu'à un bloc `try/catch` unique dans `public/index.php` :

- une `NotFoundException` (page ou livre inexistant) affiche une page **404** avec un message destiné au visiteur ;
- toute autre erreur (`Throwable`) affiche un message générique avec un code **500**, et ses détails techniques sont enregistrés dans le journal d'erreurs avec `error_log()`.

---

## Sécurité

| Risque | Protection mise en place |
|---|---|
| **Injection SQL** | Requêtes préparées pour toute donnée variable (`prepare()` / `execute()`), et désactivation de l'émulation des requêtes préparées (`PDO::ATTR_EMULATE_PREPARES => false`) |
| **Faille XSS** | Toute donnée affichée passe par `htmlspecialchars()` |
| **Mots de passe** | Hachage avec `password_hash()` (algorithme par défaut de PHP) ; le mot de passe n'est jamais réaffiché ni transmis aux vues |
| **Données de formulaire falsifiées** | Validation systématique côté serveur ; les contrôles HTML ne servent qu'au confort de l'utilisateur |
| **Paramètres d'URL inattendus** | Vérification du type (`is_string()`) et conversion explicite (`(int)`) des valeurs reçues |
| **Fuite d'informations techniques** | Messages d'erreur génériques pour le visiteur, détails consignés dans le journal |
| **Accès aux fichiers sensibles** | Seul le dossier `public/` est exposé ; la configuration et le code source restent inaccessibles |
| **Publication des identifiants** | `config/config.php` exclu du dépôt via `.gitignore` |

---

## Conventions

- **Style de code** : norme **PSR-12** (indentation de 4 espaces, accolades, nommage).
- **Nommage** : classes en `PascalCase`, méthodes et propriétés en `camelCase`, colonnes SQL en `snake_case`.
- **Commits** : format **Conventional Commits** (`feat`, `fix`, `refactor`, `chore`, `style`, `docs`…).

---

## Pistes d'amélioration

- **Conteneur d'injection de dépendances** : remplacer les instanciations directes (`new BookManager()`) et le Singleton par un conteneur qui fournit à chaque classe ses dépendances. Une proposition est disponible dans la branche `feat/router-container`.
- **Protection CSRF** des formulaires par jeton.
- **Tests automatisés** des validateurs et des managers.
