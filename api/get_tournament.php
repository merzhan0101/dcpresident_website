<?php 
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);   

require_once '../php/database.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID не передан']);
    exit;
}

$id = intval($_GET['id']);

try {
    $sql = "SELECT * FROM tournaments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tournament) {
        echo json_encode($tournament);
    } else {
        echo json_encode(['error' => 'Турнир не найден']);
    }
} catch (PDOException $e) {
    error_log('API error in api/get_tournament.php: ' . $e->getMessage());
    echo json_encode(['error' => 'Ошибка базы данных']);
}

?>