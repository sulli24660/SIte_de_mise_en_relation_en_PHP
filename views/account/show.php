<h1>Mon compte</h1>

<section class="account-profile">
    <h2><?= htmlspecialchars($user->getPseudo()) ?></h2>
    <p>Membre depuis le <?= $user->getCreatedAt()->format('d/m/Y') ?></p>

    <h3>Bibliothèque</h3>
    <p><?= count($books) ?> livre<?= count($books) > 1 ? 's' : '' ?></p>
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
