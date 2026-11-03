<header>
    <nav>
        <a href="index.php">Tom Troc</a>
        <a href="index.php">Accueil</a>
        <a href="index.php?action=books">Nos livres à l'échange</a>

        <?php if ($isLoggedIn): ?>
            <!-- Liens réservés aux membres connectés. -->
            <a href="index.php?action=messages">Messagerie</a>
            <a href="index.php?action=account">Mon compte</a>
        <?php else: ?>
            <a href="index.php?action=login">Connexion</a>
        <?php endif; ?>
    </nav>
</header>