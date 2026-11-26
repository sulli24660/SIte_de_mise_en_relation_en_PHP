<section class="hero">
    <div>
        <h1>Rejoignez nos lecteurs passionnés</h1>
        <p>Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture. Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.</p>
        <a class="button" href="index.php?action=books">Découvrir</a>
    </div>
</section>

<section class="latest-books">
    <h2>Les derniers livres ajoutés</h2>

    <?php if ($latestBooks === []): ?>
        <p>Aucun livre n'a encore été ajouté.</p>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($latestBooks as $book): ?>
                <?php require __DIR__ . '/../books/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a class="button" href="index.php?action=books">Voir tous les livres</a>
</section>

<section class="how-it-works">
    <h2>Comment ça marche ?</h2>
    <p>Échanger des livres avec TomTroc c'est simple et amusant ! Suivez ces étapes pour commencer :</p>
    <ol>
        <li>Inscrivez-vous gratuitement sur notre plateforme.</li>
        <li>Ajoutez les livres que vous souhaitez échanger à votre profil.</li>
        <li>Parcourez les livres disponibles chez d'autres membres.</li>
        <li>Proposez un échange et discutez avec d'autres passionnés de lecture.</li>
    </ol>
    <a class="button button-outline" href="index.php?action=books">Voir tous les livres</a>
</section>

<section class="values">
    <h2>Nos valeurs</h2>
    <p>Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la communauté. Nos valeurs sont ancrées dans notre passion pour les livres et notre désir de créer des liens entre les lecteurs. Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations enrichissantes.</p>
    <p>Notre association a été fondée avec une conviction profonde : chaque livre mérite d'être lu et partagé.</p>
    <p>Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger des livres qui attendent patiemment sur les étagères.</p>
    <p class="signature">L'équipe Tom Troc</p>
</section>
