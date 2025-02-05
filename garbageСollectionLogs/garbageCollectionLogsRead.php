<?php
require 'vendor/autoload.php';

use MongoDB\Client;

// Подключение к MongoDB
$mongoClient = new Client("mongodb://127.127.126.51:27017");
echo "Подключение к MongoDB успешно!  ".$mongoClient;

$db = $mongoClient->gc_logs;


$collection = $db->logs;

// Чтение всех документов из коллекции
$documents = $collection->find();

foreach ($documents as $document) {
    print_r($document);
}