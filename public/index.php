<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use App\Service\DBManager;

$pdo = DBManager::getInstance()->getPDO();

$books = $pdo->query('SELECT title FROM books')->fetchAll();

var_dump($books);
