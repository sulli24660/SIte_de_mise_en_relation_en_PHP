<section>
    <h1>Inscription</h1>

    <form action="index.php?action=register" method="post">
        <div>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" required maxlength="50" autocomplete="username">
        </div>

        <div>
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required maxlength="255" autocomplete="email">
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        </div>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="index.php?action=login">Connectez-vous</a></p>
</section>