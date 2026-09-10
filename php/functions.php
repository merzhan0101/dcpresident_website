<?php
include 'database.php';

// Получить мероприятия
function getEvents($limit = 6, $type = 'upcoming') {
    global $pdo;
    
    $sql = "SELECT * FROM events ";
    if ($type === 'upcoming') {
        $sql .= "WHERE event_date >= CURDATE() ORDER BY event_date LIMIT :limit";
    } elseif ($type === 'past') {
        $sql .= "WHERE event_date < CURDATE() ORDER BY event_date LIMIT :limit";
    } else {
        // 'all' — без фильтрации по дате, сначала самые новые (как в новостях и достижениях)
        $sql .= "ORDER BY event_date DESC LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получить достижения
function getAchievements($limit = 3, $offset = 0) {
    global $pdo;
    
    if ($limit === null) {
        $sql = "SELECT * FROM achievements ORDER BY achievement_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT * FROM achievements ORDER BY achievement_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Общее количество достижений (для пагинации)
function getAchievementsCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM achievements");
    return (int)$stmt->fetchColumn();
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

function getAllMembers() {
    global $pdo;

    try {
        $sql = "SELECT * FROM members 
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
        error_log("Database error in getAllMembers: " . $e->getMessage());
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
    return uploadOptimizedImage($file, 'members');
}

// function uploadMemberPhoto($file) {
//     $uploadDir = '../images/members/';
    
//     // Создаем папку если не существует
//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }
    
//     // Проверяем тип файла
//     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
//     $fileType = mime_content_type($file['tmp_name']);
    
//     if (!in_array($fileType, $allowedTypes)) {
//         throw new Exception('Недопустимый тип файла. Разрешены только JPEG, PNG, GIF и WebP.');
//     }
    
//     // Проверяем размер файла (максимум 5MB)
//     if ($file['size'] > 5 * 1024 * 1024) {
//         throw new Exception('Файл слишком большой. Максимальный размер - 5MB.');
//     }
    
//     // Генерируем уникальное имя файла
//     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
//     $filename = uniqid() . '.' . $extension;
//     $filepath = $uploadDir . $filename;
    
//     // Перемещаем файл
//     if (move_uploaded_file($file['tmp_name'], $filepath)) {
//         return 'images/members/' . $filename;
//     } else {
//         throw new Exception('Ошибка при загрузке файла.');
//     }
// }

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

// загрузка фото турниров
function uploadTournamentPhoto($file) {
    return uploadOptimizedImage($file, 'tournaments');
}

// function uploadTournamentPhoto($file) {
//     $uploadDir = '../images/tournaments/';

//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }

//     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
//     $fileType = mime_content_type($file['tmp_name']);

//     if (!in_array($fileType, $allowedTypes)) {
//         throw new Exception('Недопустимый тип файла.');
//     }

//     if ($file['size'] > 5 * 1024 * 1024) {
//         throw new Exception('Файл слишком большой. Максимум 5MB.');
//     }

//     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
//     $filename = uniqid() . '.' . $extension;
//     $filepath = $uploadDir . $filename;

//     if (move_uploaded_file($file['tmp_name'], $filepath)) {
//         return 'images/tournaments/' . $filename;
//     }

//     throw new Exception('Ошибка при загрузке файла.');
// }

// загрузка тизера
function uploadTournamentVideo($file)
{
    $uploadDir = '../videos/tournaments/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir,0777,true);
    }

    // разрешаемые расширения
    $allowedExtensions = ['mp4', 'mov', 'webm'];

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception('Разрешены только MP4, MOV и WEBM.');
    }

    $filename = uniqid('video_') . '.mp4';

    move_uploaded_file(
        $file['tmp_name'],
        $uploadDir . $filename
    );

    return 'videos/tournaments/' . $filename;
}

// ЗАГРУЗКА ФОТО ДЛЯ МЕРОПРИЯТИЯ
function uploadEventPhoto($file) {
    return uploadOptimizedImage($file, 'events');
}

// function uploadEventPhoto($file) {
//     $uploadDir = '../images/events/';

//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }

//     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
//     $fileType = mime_content_type($file['tmp_name']);

//     if (!in_array($fileType, $allowedTypes)) {
//         throw new Exception('Недопустимый тип файла.');
//     }

//     if ($file['size'] > 5 * 1024 * 1024) {
//         throw new Exception('Файл слишком большой. Максимум 5MB.');
//     }

//     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
//     $filename = uniqid() . '.' . $extension;
//     $filepath = $uploadDir . $filename;

//     if (move_uploaded_file($file['tmp_name'], $filepath)) {
//         return 'images/events/' . $filename;
//     }

//     throw new Exception('Ошибка при загрузке файла.');
// }

// ЗАГРУЗКА ФОТО ДЛЯ ДОСТИЖЕНИЯ
function uploadAchievementPhoto($file) {
    return uploadOptimizedImage($file, 'achievements');
}

// function uploadAchievementPhoto($file) {
//     $uploadDir = '../images/achievements/';

//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }

//     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
//     $fileType = mime_content_type($file['tmp_name']);

//     if (!in_array($fileType, $allowedTypes)) {
//         throw new Exception('Недопустимый тип файла.');
//     }

//     if ($file['size'] > 5 * 1024 * 1024) {
//         throw new Exception('Файл слишком большой. Максимум 5MB.');
//     }

//     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
//     $filename = uniqid() . '.' . $extension;
//     $filepath = $uploadDir . $filename;

//     if (move_uploaded_file($file['tmp_name'], $filepath)) {
//         return 'images/achievements/' . $filename;
//     }

//     throw new Exception('Ошибка при загрузке файла.');
// }

// ЗАГРУЗКА ФОТО ДЛЯ НОВОСТИ
function uploadNewsPhoto($file) {
    return uploadOptimizedImage($file, 'news');
}

// function uploadNewsPhoto($file) {
//     $uploadDir = '../images/news/';

//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }

//     $allowedTypes = [
//         'image/jpeg',
//         'image/png',
//         'image/gif',
//         'image/webp'
//     ];

//     $fileType = mime_content_type($file['tmp_name']);

//     if (!in_array($fileType, $allowedTypes)) {
//         throw new Exception('Недопустимый тип файла.');
//     }

//     if ($file['size'] > 5 * 1024 * 1024) {
//         throw new Exception('Файл слишком большой.');
//     }

//     $extension = pathinfo(
//         $file['name'],
//         PATHINFO_EXTENSION
//     );

//     $filename = uniqid() . '.' . $extension;

//     $filepath = $uploadDir . $filename;

//     if (move_uploaded_file($file['tmp_name'], $filepath)) {
//         return 'images/news/' . $filename;
//     }

//     throw new Exception('Ошибка загрузки файла.');
// }

// СЖАТИЕ ФОТО
function uploadOptimizedImage($file, $folder, $maxSizeMb = 25) {
    $uploadDir = "../images/$folder/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // if ($file['size'] > $maxSizeMb * 1024 * 1024) {
    //     throw new Exception("Фото не должно превышать {$maxSizeMb} МБ.");
    // }

    if ($file['size'] > $maxSizeMb * 1024 * 1024) {
        $_SESSION['error'] = "❌ Размер изображения превышает {$maxSizeMb} МБ.";
        return false;
    }

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    $mime = mime_content_type($file['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        $_SESSION['error'] = "❌ Разрешены только JPG, PNG и WEBP.";
        return false;
    }

    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $source = imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            throw new Exception('Неподдерживаемый формат.');
    }

    $width = imagesx($source);
    $height = imagesy($source);

    $maxWidth = 1600;
    $maxHeight = 1600;

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);

    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled(
        $newImage,
        $source,
        0,
        0,
        0,
        0,
        $newWidth,
        $newHeight,
        $width,
        $height
    );

    $filename = uniqid('img_') . '.jpg';
    $filepath = $uploadDir . $filename;

    imagejpeg($newImage, $filepath, 85);

    imagedestroy($source);
    imagedestroy($newImage);

    return "images/$folder/" . $filename;
}

// test
// Функция для загрузки нескольких изображений
function uploadMultipleImages($files, $folder) {
    $uploadedFiles = [];
    
    // Проверяем, что есть файлы
    if (empty($files['tmp_name'][0])) {
        return $uploadedFiles;
    }
    
    $uploadDir = "../images/$folder/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Разрешенные типы
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    
    foreach ($files['tmp_name'] as $key => $tmpName) {
        // Пропускаем пустые значения
        if (empty($tmpName) || $files['error'][$key] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        // Проверяем тип
        $mime = mime_content_type($tmpName);
        if (!in_array($mime, $allowedTypes)) {
            continue;
        }
        
        // Проверяем размер (макс 5MB)
        if ($files['size'][$key] > 5 * 1024 * 1024) {
            continue;
        }
        
        // Генерируем имя файла
        $extension = strtolower(pathinfo($files['name'][$key], PATHINFO_EXTENSION));
        $filename = uniqid('img_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Сохраняем файл
        if (move_uploaded_file($tmpName, $filepath)) {
            $uploadedFiles[] = "images/$folder/" . $filename;
        }
    }
    
    return $uploadedFiles;
}

// Функция для получения галереи
function getGalleryImages($data) {
    if (empty($data)) return [];
    
    // Если это JSON строка, парсим ее
    if (is_string($data)) {
        $gallery = json_decode($data, true);
        return is_array($gallery) ? $gallery : [];
    }
    
    // Если это уже массив
    return is_array($data) ? $data : [];
}

// Получить мероприятие по ID
function getEventById($id) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getEventById: " . $e->getMessage());
        return null;
    }
}

// Обрезает текст до нужной длины для превью в карточках (с учётом эмодзи/UTF-8)
function excerpt($text, $length = 120) {
    $text = trim($text);
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length, 'UTF-8') . '…';
}

// Возвращает список номеров страниц для отображения, с null в местах "..."
// Пример при 10 страницах и текущей = 5: [1, null, 4, 5, 6, null, 10]
function paginationRange($current, $total, $delta = 2) {
    $range = [];
    for ($i = max(1, $current - $delta); $i <= min($total, $current + $delta); $i++) {
        $range[] = $i;
    }

    $result = [];
    $prev = null;
    foreach (array_unique(array_merge([1], $range, [$total])) as $page) {
        if ($page < 1 || $page > $total) continue;
        if ($prev !== null && $page - $prev > 1) {
            $result[] = null; // многоточие
        }
        $result[] = $page;
        $prev = $page;
    }
    return $result;
}

?>