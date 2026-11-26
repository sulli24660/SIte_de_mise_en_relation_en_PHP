<section>
    <h1>Modifier les informations</h1>

    <?php
    $formAction = 'index.php?action=book-edit&id=' . $book->getId();
    require __DIR__ . '/_form.php';
    ?>

    <p><a href="index.php?action=account">Retour à mon compte</a></p>
</section>
