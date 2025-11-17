<?php
include '../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $achievement = getAchievementById($_GET['id']);
    echo json_encode($achievement);
}
?>