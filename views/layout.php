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
    <?php include '_header.php'; ?>
    <main>
        <?php require $templatePath ?>
    </main>
    <?php include '_footer.php'; ?>
</body>

</html>