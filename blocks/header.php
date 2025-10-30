<header>
  <div class="container header-container">
  <a href="index.php" style="text-decoration: none;">
      <div class="logo">
          <img src="images/logo_president.png" alt="DC President Logo">
          <div class="logo-text">
              <p class="club">DC President</p>
              <p class="university">Toraighyrov University</p>
          </div>
      </div>
  </a>
  <!-- <div class="logo">
      <img src="images/logo_president.png" alt="Debate Club President Logo">
      <div class="logo-text">
        <p class="club">DC President</p>
        <p class="university">Toraighyrov University</p>
      </div>
    </div> -->

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
      <a href="index.php#apply" class="join-btn">Присоединиться</a>
    </div>
  </div>
</header>


<style>
  /* Стиль для логотипа как ссылки */
  .header-container a:first-child {
      text-decoration: none;
      transition: opacity 0.3s ease;
  }

  .header-container a:first-child:hover {
      opacity: 0.8;
  }

  @media (max-width: 900px) {
    .header-container {
        flex-direction: column;
        gap: 10px;
    }
    
    .logo-link {
        justify-content: center;
    }
}
</style>