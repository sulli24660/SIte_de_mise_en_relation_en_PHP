<article class="book-card">
    <a href="index.php?action=book&id=<?= $book->getId() ?>">
        <?php if ($book->getImage() !== null): ?>
            <img src="uploads/books/<?= htmlspecialchars($book->getImage()) ?>" alt="" width="200">
        <?php endif; ?>
        <?php if (!$book->isAvailable()): ?>
            <span class="badge-unavailable">non dispo.</span>
        <?php endif; ?>
        <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
    </a>
    <p><?= htmlspecialchars($book->getAuthor()) ?></p>
    <?php if ($book->getOwnerPseudo() !== null): ?>
        <p class="book-card-owner">Vendu par : <?= htmlspecialchars($book->getOwnerPseudo()) ?></p>
    <?php endif; ?>
</article>
