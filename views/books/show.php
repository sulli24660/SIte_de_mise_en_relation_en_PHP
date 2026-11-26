<nav aria-label="Fil d'Ariane">
    <a href="index.php?action=books">Nos livres</a> &gt; <?= htmlspecialchars($book->getTitle()) ?>
</nav>

<article class="book-detail">
    <?php if ($book->getImage() !== null): ?>
        <img src="uploads/books/<?= htmlspecialchars($book->getImage()) ?>" alt="Couverture de « <?= htmlspecialchars($book->getTitle()) ?> »" width="300">
    <?php endif; ?>

    <div>
        <h1><?= htmlspecialchars($book->getTitle()) ?></h1>
        <p>par <?= htmlspecialchars($book->getAuthor()) ?></p>
        <p><?= $book->isAvailable() ? 'disponible' : 'non dispo.' ?></p>

        <h2>Description</h2>
        <p><?= nl2br(htmlspecialchars($book->getDescription() ?? '')) ?></p>

        <?php if ($owner !== null): ?>
            <h2>Propriétaire</h2>
            <p><a href="index.php?action=profile&id=<?= $owner->getId() ?>"><?= htmlspecialchars($owner->getPseudo()) ?></a></p>

            <?php if (!$isOwnBook): ?>
                <a href="index.php?action=messages&with=<?= $owner->getId() ?>">Envoyer un message</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</article>
