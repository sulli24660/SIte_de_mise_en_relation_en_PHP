<h1>Nos livres à l'échange</h1>

<ul>
    <?php foreach ($books as $book): ?>
        <li>
            <article>
                <h2>
                    <a href="index.php?action=book&id=<?= $book->getId() ?>">
                        <?= htmlspecialchars($book->getTitle()) ?>
                    </a>
                </h2>
                <p>par <?= htmlspecialchars($book->getAuthor()) ?></p>
            </article>
        </li>
    <?php endforeach; ?>
</ul>