<?php
$values = [
    'title' => $old['title'] ?? $book?->getTitle() ?? '',
    'author' => $old['author'] ?? $book?->getAuthor() ?? '',
    'description' => $old['description'] ?? $book?->getDescription() ?? '',
    'is_available' => $old['is_available'] ?? ($book === null || $book->isAvailable() ? '1' : '0'),
];
?>

<?php if ($errors->has('form')): ?>
    <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
<?php endif; ?>

<form action="<?= htmlspecialchars($formAction) ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div>
        <label for="title">Titre</label>
        <input type="text" id="title" name="title"
            value="<?= htmlspecialchars($values['title']) ?>"
            required maxlength="255">
        <?php if ($errors->has('title')): ?>
            <p class="form-error"><?= htmlspecialchars($errors->get('title')) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="author">Auteur</label>
        <input type="text" id="author" name="author"
            value="<?= htmlspecialchars($values['author']) ?>"
            required maxlength="255">
        <?php if ($errors->has('author')): ?>
            <p class="form-error"><?= htmlspecialchars($errors->get('author')) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="description">Commentaire</label>
        <textarea id="description" name="description" rows="8" maxlength="2000"><?= htmlspecialchars($values['description']) ?></textarea>
        <?php if ($errors->has('description')): ?>
            <p class="form-error"><?= htmlspecialchars($errors->get('description')) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="is_available">Disponibilité</label>
        <select id="is_available" name="is_available">
            <option value="1" <?= $values['is_available'] === '1' ? 'selected' : '' ?>>disponible</option>
            <option value="0" <?= $values['is_available'] === '0' ? 'selected' : '' ?>>non dispo.</option>
        </select>
        <?php if ($errors->has('is_available')): ?>
            <p class="form-error"><?= htmlspecialchars($errors->get('is_available')) ?></p>
        <?php endif; ?>
    </div>

    <button type="submit">Valider</button>
</form>
