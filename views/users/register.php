<section>
    <h1>Inscription</h1>

    <form action="index.php?action=register" method="post">
        <div>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" required maxlength="50" autocomplete="username"
                   value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>">
            <?php if ($errors->has('pseudo')): ?>
                <p><?= htmlspecialchars($errors->first('pseudo')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required maxlength="255" autocomplete="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            <?php if ($errors->has('email')): ?>
                <p><?= htmlspecialchars($errors->first('email')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <?php if ($errors->has('password')): ?>
                <p><?= htmlspecialchars($errors->first('password')) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="index.php?action=login">Connectez-vous</a></p>
</section>
