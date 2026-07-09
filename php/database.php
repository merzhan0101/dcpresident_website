<?php

$host = 'localhost';
$dbname = 'bd_dcpresident';
$username = 'root';
$password = '';

// $host = 'sql202.infinityfree.com';
// $dbname = 'if0_42295670_bd_dcpresident';
// $username = 'if0_42295670';
// $password = 'rJdd0ikEDGDMhTE';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

?>