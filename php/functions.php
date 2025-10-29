<?php
include 'database.php';

// Получить мероприятия
function getEvents($limit = 6, $type = 'upcoming') {
    global $pdo;
    
    $sql = "SELECT * FROM events ";
    if ($type === 'upcoming') {
        $sql .= "WHERE event_date >= CURDATE() ";
    } else {
        $sql .= "WHERE event_date < CURDATE() ";
    }
    $sql .= "ORDER BY event_date LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получить достижения
function getAchievements($limit = 3) {
    global $pdo;
    
    $sql = "SELECT * FROM achievements ORDER BY achievement_date DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получить новости
function getNews($limit = 2) {
    global $pdo;
    
    $sql = "SELECT * FROM news ORDER BY news_date DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRussianMonth($monthNumber) {
    $months = [
        1 => 'Янв', 2 => 'Фев', 3 => 'Мар', 4 => 'Апр',
        5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Авг',
        9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'
    ];
    return $months[$monthNumber];
}

?>