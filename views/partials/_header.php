<header>
    <nav>
        <a href="index.php?action=home">Tom Troc</a>
        <a href="index.php?action=home">Accueil</a>
        <a href="index.php?action=books">Nos livres à l'échange</a>

        <?php if ($isLoggedIn): ?>
            <a href="index.php?action=messages">
                Messagerie
                <?php if ($unreadMessagesCount > 0): ?>
                    <span class="badge-count" aria-label="<?= $unreadMessagesCount ?> message(s) non lu(s)"><?= $unreadMessagesCount ?></span>
                <?php endif; ?>
            </a>
            <a href="index.php?action=account">Mon compte</a>

            <form action="index.php?action=logout" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button type="submit">Déconnexion</button>
            </form>
        <?php else: ?>
            <a href="index.php?action=login">Connexion</a>
        <?php endif; ?>
    </nav>
</header>