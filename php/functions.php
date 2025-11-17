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
        1 => 'Қаңтар', 2 => 'Ақпан', 3 => 'Наурыз', 4 => 'Сәуір',
        5 => 'Мамыр', 6 => 'Маусым', 7 => 'Шілде', 8 => 'Тамыз',
        9 => 'Қыркүйек', 10 => 'Қазан', 11 => 'Қараша', 12 => 'Желтоқсан'
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
                WHERE role IN ('президент', 'координатор', 'бас бапкер', 'pr') 
                AND is_active = TRUE 
                ORDER BY 
                    CASE role 
                        WHEN 'президент' THEN 1
                        WHEN 'координатор' THEN 2 
                        WHEN 'бас бапкер' THEN 3
                        WHEN 'pr' THEN 4
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

// Загрузить фото участника
function uploadMemberPhoto($file) {
    $uploadDir = '../images/members/';
    
    // Создаем папку если не существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Проверяем тип файла
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Недопустимый тип файла. Разрешены только JPEG, PNG, GIF и WebP.');
    }
    
    // Проверяем размер файла (максимум 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Файл слишком большой. Максимальный размер - 5MB.');
    }
    
    // Генерируем уникальное имя файла
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Перемещаем файл
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'images/members/' . $filename;
    } else {
        throw new Exception('Ошибка при загрузке файла.');
    }
}

// Функция для получения названия месяца
function getRussianMonthName($monthNumber) {
    $months = [
        1 => 'қаңтар', 2 => 'ақпан', 3 => 'наурыз', 4 => 'сәуір',
        5 => 'мамыр', 6 => 'маусым', 7 => 'шілде', 8 => 'тамыз',
        9 => 'қыркүйек', 10 => 'қазан', 11 => 'қараша', 12 => 'желтоқсан'
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
                         (birth_month = MONTH(CURDATE()) AND birth_day > DAY(CURDATE())) 
                    THEN (birth_month * 100 + birth_day) 
                    ELSE (birth_month * 100 + birth_day + 1200) 
                END as birthday_order
                FROM members 
                WHERE is_active = TRUE 
                AND birth_month IS NOT NULL 
                AND birth_day IS NOT NULL
                AND birth_month BETWEEN 1 AND 12 
                AND birth_day BETWEEN 1 AND 31
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

// function getUpcomingBirthdays($limit = 5) {
//     global $pdo;
    
//     try {
//         $sql = "SELECT *, 
//                 CASE 
//                     WHEN (birth_month > MONTH(CURDATE())) OR 
//                          (birth_month = MONTH(CURDATE()) AND birth_day >= DAY(CURDATE())) 
//                     THEN (birth_month * 100 + birth_day) 
//                     ELSE (birth_month * 100 + birth_day + 1200) 
//                 END as birthday_order
//                 FROM members 
//                 WHERE is_active = TRUE 
//                 ORDER BY birthday_order 
//                 LIMIT :limit";
        
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//         $stmt->execute();
        
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Database error in getUpcomingBirthdays: " . $e->getMessage());
//         return [];
//     }
// }

// Функция для расчета дней до дня рождения
function daysUntilBirthday($birthMonth, $birthDay) {
    // Проверка на валидность данных
    if (empty($birthMonth) || empty($birthDay) || 
        !is_numeric($birthMonth) || !is_numeric($birthDay) ||
        $birthMonth < 1 || $birthMonth > 12 || 
        $birthDay < 1 || $birthDay > 31) {
        return null;
    }
    
    try {
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Устанавливаем время на начало дня
        
        $currentYear = (int)$today->format('Y');
        
        // Создаем дату дня рождения в текущем году
        $birthdayThisYear = new DateTime("$currentYear-$birthMonth-$birthDay");
        $birthdayThisYear->setTime(0, 0, 0);
        
        // Если день рождения в этом году уже прошел (но не сегодня), берем следующий год
        if ($birthdayThisYear < $today) {
            $birthdayNextYear = new DateTime(($currentYear + 1) . "-$birthMonth-$birthDay");
            $birthdayNextYear->setTime(0, 0, 0);
            $interval = $today->diff($birthdayNextYear);
        } else {
            $interval = $today->diff($birthdayThisYear);
        }
        
        return (int)$interval->days;
        
    } catch (Exception $e) {
        error_log("Error calculating birthday: " . $e->getMessage());
        return null;
    }
}

// function daysUntilBirthday($birthMonth, $birthDay) {
//     // Проверка на валидность данных
//     if (empty($birthMonth) || empty($birthDay) || 
//         !is_numeric($birthMonth) || !is_numeric($birthDay) ||
//         $birthMonth < 1 || $birthMonth > 12 || 
//         $birthDay < 1 || $birthDay > 31) {
//         return null; // или можно вернуть -1 или другое значение по умолчанию
//     }
    
//     try {
//         $today = new DateTime();
//         echo $today->format('Y-m-d');
//         $currentYear = (int)$today->format('Y');
        
//         // Создаем дату дня рождения с проверкой валидности
//         $birthdayString = sprintf("%d-%02d-%02d", $currentYear, $birthMonth, $birthDay);
//         $birthdayThisYear = new DateTime($birthdayString);
//         $birthdayNextYear = new DateTime(($currentYear + 1) . "-" . sprintf("%02d", $birthMonth) . "-" . sprintf("%02d", $birthDay));
        
//         if ($birthdayThisYear >= $today) {
//             $interval = $today->diff($birthdayThisYear);
//         } else {
//             $interval = $today->diff($birthdayNextYear);
//         }
        
//         return (int)$interval->format('%a');
//     } catch (Exception $e) {
//         error_log("Error calculating birthday: " . $e->getMessage());
//         return null;
//     }
// }

// function daysUntilBirthday($birthMonth, $birthDay) {
//     $today = new DateTime();
//     $currentYear = (int)$today->format('Y');
    
//     $birthdayThisYear = new DateTime("$currentYear-$birthMonth-$birthDay");
//     $birthdayNextYear = new DateTime(($currentYear + 1) . "-$birthMonth-$birthDay");
    
//     if ($birthdayThisYear >= $today) {
//         $interval = $today->diff($birthdayThisYear);
//     } else {
//         $interval = $today->diff($birthdayNextYear);
//     }
    
//     return (int)$interval->format('%a');
// }

// Функция для правильного склонения слова "день"
function getRussianDaysWord($days) {
    if ($days % 10 == 1 && $days % 100 != 11) {
        return 'күн'; // 'день' на казахском
    } elseif ($days % 10 >= 2 && $days % 10 <= 4 && ($days % 100 < 10 || $days % 100 >= 20)) {
        return 'кейін'; // 'дня' на казахском
    } else {
        return 'кейін'; // 'дней' на казахском
    }
}


// ТУРНИРЫ =====================================================================
// Получить все турниры
function getAllTournaments() {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM tournaments ORDER BY start_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllTournaments: " . $e->getMessage());
        return [];
    }
}

// Получить турнир по ID
function getTournamentById($id) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM tournaments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getTournamentById: " . $e->getMessage());
        return null;
    }
}

// Получить турниры по уровню
function getTournamentsByLevel($level) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM tournaments WHERE level = :level ORDER BY start_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':level', $level);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getTournamentsByLevel: " . $e->getMessage());
        return [];
    }
}

// Получить ближайшие турниры
function getUpcomingTournaments($limit = 3) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM tournaments 
                WHERE start_date >= CURDATE() 
                ORDER BY start_date ASC 
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getUpcomingTournaments: " . $e->getMessage());
        return [];
    }
}


// ADMIN PANEL =====================================================================
// Функции для работы с пользователями
function getUserByUsername($username) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM users WHERE username = :username AND is_active = TRUE";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getUserByUsername: " . $e->getMessage());
        return null;
    }
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: login.php");
        exit;
    }
}

// Обновляем время последнего входа
function updateLastLogin($user_id) {
    global $pdo;
    
    try {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database error in updateLastLogin: " . $e->getMessage());
    }
}


?>