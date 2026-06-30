<?php
include '../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $event = getEventById($_GET['id']);
    echo json_encode($event);
}

function getEventById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>