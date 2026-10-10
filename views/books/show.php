<article>
    <h1><?= htmlspecialchars($book->getTitle()) ?></h1>
    <p>par <?= htmlspecialchars($book->getAuthor()) ?></p>

    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($book->getDescription() ?? '')) ?></p>
</article>