<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../php/database.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID не передан']);
    exit;
}

$id = intval($_GET['id']);

try {
    $sql = "SELECT * FROM members WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($member) {
        echo json_encode($member);
    } else {
        echo json_encode(['error' => 'Участник не найден']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>