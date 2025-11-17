<?php
include '../php/functions.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $news = getNewsById($_GET['id']);
    echo json_encode($news);
}
?>