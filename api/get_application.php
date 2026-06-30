<?php
include '../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($application);
}
?>