<section class="catalog">
    <div class="catalog-head">
        <h1>Nos livres à l'échange</h1>

        <form action="index.php" method="get" role="search">
            <input type="hidden" name="action" value="books">
            <label for="q" class="visually-hidden">Rechercher un livre</label>
            <input type="search" id="q" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un livre" maxlength="255">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($search !== ''): ?>
        <p>
            <?= count($books) ?> résultat<?= count($books) > 1 ? 's' : '' ?> pour « <?= htmlspecialchars($search) ?> »
            · <a href="index.php?action=books">Voir tous les livres</a>
        </p>
    <?php endif; ?>

    <?php if ($books === []): ?>
        <p>Aucun livre ne correspond à votre recherche.</p>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($books as $book): ?>
                <?php require __DIR__ . '/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
