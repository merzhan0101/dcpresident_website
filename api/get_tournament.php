<?php
include '../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $tournament = getTournamentById($_GET['id']);
    echo json_encode($tournament);
}
?>