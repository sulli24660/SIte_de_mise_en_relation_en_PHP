<header>
    <nav>
        <a href="index.php">Tom Troc</a>
        <a href="index.php">Accueil</a>
        <a href="index.php?action=books">Nos livres à l'échange</a>

        <?php if ($isLoggedIn): ?>
            <!-- Liens réservés aux membres connectés. -->
            <a href="index.php?action=messages">Messagerie</a>
            <a href="index.php?action=account">Mon compte</a>

            <!-- Déconnexion en POST avec jeton CSRF : un simple lien pourrait
                 être déclenché à l'insu de l'utilisateur depuis un autre site. -->
            <form action="index.php?action=logout" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button type="submit">Déconnexion</button>
            </form>
        <?php else: ?>
            <a href="index.php?action=login">Connexion</a>
        <?php endif; ?>
    </nav>
</header>