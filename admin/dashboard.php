<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

$stats = [
    'members' => count(getAllActiveMembers()),
    'tournaments' => count(getAllTournaments()),
    'events' => count(getAllEvents()),
    'news' => count(getAllNews()),
    'achievements' => count(getAllAchievements()),
    'applications' => getApplicationsCount()
];

function getApplicationsCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'новая'");
    return $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--gray);
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        
        .sidebar-nav a {
            display: block;
            padding: 12px 20px;
            color: var(--text);
            text-decoration: none;
            transition: background 0.3s ease;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(181, 0, 0, 0.2);
            color: var(--red);
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            background: var(--bg);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--gray);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: var(--red);
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: var(--text);
            font-size: 14px;
        }
        
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .action-card {
            background: var(--gray);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
        }
        
        .action-card h3 {
            margin-bottom: 15px;
            color: var(--text);
        }
        
        .btn-admin {
            display: inline-block;
            padding: 10px 20px;
            background: var(--red);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        
        .btn-admin:hover {
            background: #9b0000;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Боковая панель -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 style="color: var(--red);">DC President</h3>
                <small style="color: #888;">Панель управления</small>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">📊 Дашборд</a>
                <a href="members.php">👥 Участники</a>
                <a href="tournaments.php">🏆 Турниры</a>
                <a href="events.php">📅 Мероприятия</a>
                <a href="achievements.php">🎯 Достижения</a>
                <a href="news.php">📰 Новости</a>
                <a href="applications.php">📋 Заявки</a>
                <a href="../index.php">🌐 На сайт</a>
                <a href="logout.php">🚪 Выйти</a>
            </nav>
        </div>
        
        <!-- Основной контент -->
        <div class="main-content">
            <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['full_name']) ?>!</h1>
            <p>Панель управления дебатным клубом DC President</p>
            
            <!-- Статистика -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['members'] ?></div>
                    <div class="stat-label">Участников</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['tournaments'] ?></div>
                    <div class="stat-label">Турниров</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['events'] ?></div>
                    <div class="stat-label">Мероприятий</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['news'] ?></div>
                    <div class="stat-label">Новостей</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['achievements'] ?></div>
                    <div class="stat-label">Достижений</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['applications'] ?></div>
                    <div class="stat-label">Новых заявок</div>
                </div>
            </div>
            
            <!-- Быстрые действия -->
            <h2>Быстрые действия</h2>
            <div class="admin-actions">
                <div class="action-card">
                    <h3>Добавить участника</h3>
                    <a href="members.php?action=add" class="btn-admin">Добавить</a>
                </div>
                <div class="action-card">
                    <h3>Создать турнир</h3>
                    <a href="tournaments.php?action=add" class="btn-admin">Создать</a>
                </div>
                <div class="action-card">
                    <h3>Добавить мероприятие</h3>
                    <a href="events.php?action=add" class="btn-admin">Добавить</a>
                </div>
                <div class="action-card">
                    <h3>Опубликовать новость</h3>
                    <a href="news.php?action=add" class="btn-admin">Опубликовать</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>