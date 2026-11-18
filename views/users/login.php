<section>
    <h1>Connexion</h1>

    <?php if ($errors->has('form')): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
    <?php endif; ?>

    <form action="index.php?action=login" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                required maxlength="255" autocomplete="email">
            <?php if ($errors->has('email')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('email')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                required autocomplete="current-password">
            <?php if ($errors->has('password')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('password')) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="index.php?action=register">Inscrivez-vous</a></p>
</section>
