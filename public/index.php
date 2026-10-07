<?php
require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Model\BookManager;

$bookManager = new BookManager();
$books = $bookManager->findAll();

foreach ($books as $book) {
    echo '<p>' . htmlspecialchars($book->getTitle()) . ' — ' . htmlspecialchars($book->getAuthor()) . '</p>';
}
