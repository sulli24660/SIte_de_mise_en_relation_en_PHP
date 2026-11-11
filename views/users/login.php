<section>
    <h1>Connexion</h1>

    <?php if ($errors?->has('form')): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
    <?php endif; ?>

    <form action="index.php?action=login" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($email) ?>"
                required maxlength="255" autocomplete="email">
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                required autocomplete="current-password">
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="index.php?action=register">Inscrivez-vous</a></p>
</section>