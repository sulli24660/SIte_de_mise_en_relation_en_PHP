<section class="profile-card">
    <h1><?= htmlspecialchars($member->getPseudo()) ?></h1>
    <p>Membre depuis le <?= $member->getCreatedAt()->format('d/m/Y') ?></p>

    <h2>Bibliothèque</h2>
    <p><?= count($books) ?> livre<?= count($books) > 1 ? 's' : '' ?></p>

    <?php if ($isOwnProfile): ?>
        <a href="index.php?action=account">Gérer mon compte</a>
    <?php else: ?>
        <a href="index.php?action=messages&with=<?= $member->getId() ?>">Écrire un message</a>
    <?php endif; ?>
</section>

<section class="profile-library">
    <h2>Sa bibliothèque</h2>

    <?php if ($books === []): ?>
        <p>Ce membre n'a pas encore ajouté de livre.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th scope="col">Photo</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <?php if ($book->getImage() !== null): ?>
                                <img src="uploads/books/<?= htmlspecialchars($book->getImage()) ?>" alt="" width="60">
                            <?php endif; ?>
                        </td>
                        <td><a href="index.php?action=book&id=<?= $book->getId() ?>"><?= htmlspecialchars($book->getTitle()) ?></a></td>
                        <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($book->getDescription() ?? '', 0, 90, '…')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
