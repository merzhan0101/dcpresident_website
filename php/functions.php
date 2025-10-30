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

// FOR BLOCKS
// В functions.php добавьте:

// Получить все мероприятия (без лимита)
function getAllEvents($type = 'all') {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM events ";
        if ($type === 'upcoming') {
            $sql .= "WHERE event_date >= CURDATE() ";
        } elseif ($type === 'past') {
            $sql .= "WHERE event_date < CURDATE() ";
        }
        $sql .= "ORDER BY event_date DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllEvents: " . $e->getMessage());
        return [];
    }
}

// Получить достижение по ID
function getAchievementById($id) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM achievements WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAchievementById: " . $e->getMessage());
        return null;
    }
}

// Получить все достижения для страницы achievements.php
function getAllAchievements() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM achievements ORDER BY achievement_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllAchievements: " . $e->getMessage());
        return [];
    }
}



// Получить все достижения (без лимита)
// function getAllAchievements() {
//     global $pdo;
    
//     try {
//         $sql = "SELECT * FROM achievements ORDER BY achievement_date DESC";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute();
        
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Database error in getAllAchievements: " . $e->getMessage());
//         return [];
//     }
// }

// Получить все новости для страницы news.php
function getAllNews() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM news ORDER BY news_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllNews: " . $e->getMessage());
        return [];
    }
}

// Получить новость по ID
function getNewsById($id) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM news WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getNewsById: " . $e->getMessage());
        return null;
    }
}


// function getAllNews() {
//     global $pdo;
    
//     try {
//         $sql = "SELECT * FROM news ORDER BY news_date DESC";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute();
        
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Database error in getAllNews: " . $e->getMessage());
//         return [];
//     }
// } 




// УЧАСТНИКИ КЛУБА =====================================================================
// Получить руководство клуба (президент, вице-президент, координатор, тренер)
function getClubLeadership() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM members 
                WHERE role IN ('президент', 'вице-президент', 'координатор', 'тренер') 
                AND is_active = TRUE 
                ORDER BY 
                    CASE role 
                        WHEN 'президент' THEN 1
                        WHEN 'вице-президент' THEN 2 
                        WHEN 'тренер' THEN 3
                        WHEN 'координатор' THEN 4
                        ELSE 5
                    END";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getClubLeadership: " . $e->getMessage());
        return [];
    }
}

// Получить всех активных участников
function getAllActiveMembers() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM members 
                WHERE is_active = TRUE 
                ORDER BY 
                    CASE role 
                        WHEN 'президент' THEN 1
                        WHEN 'вице-президент' THEN 2 
                        WHEN 'тренер' THEN 3
                        WHEN 'координатор' THEN 4
                        ELSE 5
                    END, full_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllActiveMembers: " . $e->getMessage());
        return [];
    }
}

// Получить участников по поколению
function getMembersByGeneration($generation) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM members 
                WHERE generation = :generation AND is_active = TRUE 
                ORDER BY full_name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':generation', $generation);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getMembersByGeneration: " . $e->getMessage());
        return [];
    }
}

// Функция для получения названия месяца
function getRussianMonthName($monthNumber) {
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ];
    return $months[$monthNumber];
}


// Получить участников с ближайшими днями рождениями
function getUpcomingBirthdays($limit = 5) {
    global $pdo;
    
    try {
        $sql = "SELECT *, 
                CASE 
                    WHEN (birth_month > MONTH(CURDATE())) OR 
                         (birth_month = MONTH(CURDATE()) AND birth_day >= DAY(CURDATE())) 
                    THEN (birth_month * 100 + birth_day) 
                    ELSE (birth_month * 100 + birth_day + 1200) 
                END as birthday_order
                FROM members 
                WHERE is_active = TRUE 
                ORDER BY birthday_order 
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getUpcomingBirthdays: " . $e->getMessage());
        return [];
    }
}

// Функция для расчета дней до дня рождения
function daysUntilBirthday($birthMonth, $birthDay) {
    $today = new DateTime();
    $currentYear = (int)$today->format('Y');
    
    $birthdayThisYear = new DateTime("$currentYear-$birthMonth-$birthDay");
    $birthdayNextYear = new DateTime(($currentYear + 1) . "-$birthMonth-$birthDay");
    
    if ($birthdayThisYear >= $today) {
        $interval = $today->diff($birthdayThisYear);
    } else {
        $interval = $today->diff($birthdayNextYear);
    }
    
    return (int)$interval->format('%a');
}

// Функция для правильного склонения слова "день"
function getRussianDaysWord($days) {
    if ($days % 10 == 1 && $days % 100 != 11) {
        return 'день';
    } elseif ($days % 10 >= 2 && $days % 10 <= 4 && ($days % 100 < 10 || $days % 100 >= 20)) {
        return 'дня';
    } else {
        return 'дней';
    }
}


?>