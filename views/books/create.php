<section>
    <h1>Ajouter un livre</h1>

    <?php if ($errors->has('form')): ?>
        <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
    <?php endif; ?>

    <form action="index.php?action=book-create" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="title">Titre</label>
            <input type="text" id="title" name="title"
                value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                required maxlength="255">
            <?php if ($errors->has('title')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('title')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="author">Auteur</label>
            <input type="text" id="author" name="author"
                value="<?= htmlspecialchars($old['author'] ?? '') ?>"
                required maxlength="255">
            <?php if ($errors->has('author')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('author')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="description">Commentaire</label>
            <textarea id="description" name="description" rows="8" maxlength="2000"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            <?php if ($errors->has('description')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('description')) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="is_available">Disponibilité</label>
            <select id="is_available" name="is_available">
                <option value="1" <?= ($old['is_available'] ?? '1') === '1' ? 'selected' : '' ?>>disponible</option>
                <option value="0" <?= ($old['is_available'] ?? '1') === '0' ? 'selected' : '' ?>>non dispo.</option>
            </select>
            <?php if ($errors->has('is_available')): ?>
                <p class="form-error"><?= htmlspecialchars($errors->get('is_available')) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Valider</button>
    </form>

    <p><a href="index.php?action=account">Retour à mon compte</a></p>
</section>
