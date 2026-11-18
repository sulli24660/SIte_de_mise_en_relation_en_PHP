<h1>Mon compte</h1>

<section class="account-profile">
    <h2><?= htmlspecialchars($user->getPseudo()) ?></h2>
    <p>Membre depuis le <?= $user->getCreatedAt()->format('d/m/Y') ?></p>

    <h3>Bibliothèque</h3>
    <p><?= count($books) ?> livre<?= count($books) > 1 ? 's' : '' ?></p>
</section>

<section class="account-settings">
    <h2>Vos informations personnelles</h2>

    <?php if ($errors->has('form')): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
    <?php endif; ?>

    <form action="index.php?action=account" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($old['email'] ?? $user->getEmail()) ?>"
                required maxlength="255" autocomplete="email">
            <?php if ($errors->has('email')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('email')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"
                minlength="8" autocomplete="new-password" aria-describedby="password-help">
            <p id="password-help">Laissez vide pour conserver votre mot de passe actuel.</p>
            <?php if ($errors->has('password')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('password')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="pseudo">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo"
                value="<?= htmlspecialchars($old['pseudo'] ?? $user->getPseudo()) ?>"
                required minlength="3" maxlength="50" autocomplete="username">
            <?php if ($errors->has('pseudo')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('pseudo')) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</section>

<section class="account-library">
    <h2>Ma bibliothèque</h2>

    <?php if ($books === []): ?>
        <p>Vous n'avez encore ajouté aucun livre.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Description</th>
                    <th scope="col">Disponibilité</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book->getTitle()) ?></td>
                        <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($book->getDescription() ?? '', 0, 90, '…')) ?></td>
                        <td><?= $book->isAvailable() ? 'disponible' : 'non dispo.' ?></td>
                        <td>
                            <a href="index.php?action=book-edit&id=<?= $book->getId() ?>">Éditer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
