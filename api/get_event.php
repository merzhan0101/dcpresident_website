<?php
// Включаем отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаем базу данных
require_once '../php/database.php';

// Устанавливаем заголовок JSON
header('Content-Type: application/json');

// Проверяем, что передан ID
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID не передан']);
    exit;
}

$id = intval($_GET['id']);

try {
    // Получаем данные мероприятия
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        echo json_encode($event);
    } else {
        echo json_encode(['error' => 'Мероприятие не найдено']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>