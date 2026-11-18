<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titre de la page</title>
    <meta name="description" content="Description de la page">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
     <?php require __DIR__ . '/partials/_header.php'; ?>
    <main>
        <?php if ($success !== null): ?>
            <p class="flash-success" role="status"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <?php require $templatePath ?>
    </main>
    <?php require __DIR__ . '/partials/_footer.php'; ?>
</body>

</html>