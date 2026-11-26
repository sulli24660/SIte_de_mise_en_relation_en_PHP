<article>
    <?php if ($book->getImage() !== null): ?>
        <img src="uploads/books/<?= htmlspecialchars($book->getImage()) ?>" alt="Couverture de « <?= htmlspecialchars($book->getTitle()) ?> »" width="300">
    <?php endif; ?>
    <h1><?= htmlspecialchars($book->getTitle()) ?></h1>
    <p>par <?= htmlspecialchars($book->getAuthor()) ?></p>

    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($book->getDescription() ?? '')) ?></p>
</article>