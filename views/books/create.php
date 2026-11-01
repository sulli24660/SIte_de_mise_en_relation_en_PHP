<section>
    <h1>Ajouter un livre</h1>

    <?php if ($errors->any()): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="index.php?action=book-store" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" required maxlength="255"
                   value="<?= htmlspecialchars($old['title'] ?? '') ?>">
        </div>

        <div>
            <label for="author">Auteur</label>
            <input type="text" id="author" name="author" required maxlength="255"
                   value="<?= htmlspecialchars($old['author'] ?? '') ?>">
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div>
            <input type="checkbox" id="is_available" name="is_available" value="1"
                <?= ($old['is_available'] ?? true) ? 'checked' : '' ?>>
            <label for="is_available">Disponible à l'échange</label>
        </div>

        <button type="submit">Ajouter</button>
    </form>
</section>
