<div class="sidebar">
    <div class="sidebar-header">
        <h3 style="color: var(--red);">DC President</h3>
        <small style="color: #888;">Панель управления</small>
        <div style="margin-top: 10px; font-size: 12px; color: #4caf50;">
            👋 <?= htmlspecialchars($_SESSION['user']['full_name']) ?>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">📊 Дашборд</a>
        <a href="members.php" class="<?= basename($_SERVER['PHP_SELF']) == 'members.php' ? 'active' : '' ?>">👥 Участники</a>
        <a href="tournaments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tournaments.php' ? 'active' : '' ?>">🏆 Турниры</a>
        <a href="events.php" class="<?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">📅 Мероприятия</a>
        <a href="achievements.php" class="<?= basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'active' : '' ?>">🎯 Достижения</a>
        <a href="news.php" class="<?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">📰 Новости</a>
        <a href="applications.php" class="<?= basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : '' ?>">📋 Заявки</a>
        <!-- <a href="../index.php">🌐 На сайт</a> -->
        <a href="logout.php">🚪 Выйти</a>
    </nav>
</div>