<header>
  <div class="container header-container">
    <div class="logo">
      <img src="images/logo_president.png" alt="Debate Club President Logo">
      <div class="logo-text">
        <p class="club">DC President</p>
        <p class="university">Toraighyrov University</p>
      </div>
    </div>

    <nav>
      <ul>
        <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Главная</a></li>
        <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">О клубе</a></li>
        <li><a href="events.php" class="<?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">Мероприятия</a></li>
        <li><a href="achievements.php" class="<?= basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'active' : '' ?>">Достижения</a></li>
        <li><a href="news.php" class="<?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">Новости</a></li>
        <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Контакты</a></li>
      </ul>
    </nav>

    <div class="header-btn">
      <a href="submit.php" class="join-btn">Присоединиться</a>
    </div>
  </div>
</header>
