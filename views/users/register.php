<section>
    <h1>Inscription</h1>

    <?php if ($errors?->has('form')): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
    <?php endif; ?>

    <form action="index.php?action=register" method="post">
        <!-- Jeton anti-CSRF : prouve que le formulaire vient bien de TomTroc. -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo"
                value="<?= htmlspecialchars($pseudo) ?>"
                required minlength="3" maxlength="50" autocomplete="username">
            <?php if ($errors?->has('pseudo')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('pseudo')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($email) ?>"
                required maxlength="255" autocomplete="email">
            <?php if ($errors?->has('email')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('email')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <!-- Pas d'attribut value : le mot de passe n'est jamais réaffiché. -->
            <input type="password" id="password" name="password"
                required minlength="8" autocomplete="new-password">
            <?php if ($errors?->has('password')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('password')) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="index.php?action=login">Connectez-vous</a></p>
</section>