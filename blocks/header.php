<header>
  <div class="container1 header-container">
  <a href="index.php" style="text-decoration: none;">
      <div class="logo">
          <img src="images/logo_president.png" alt="DC President Logo">
          <div class="logo-text">
              <p class="club">DC President</p>
              <p class="university">Toraighyrov University</p>
          </div>
      </div>
  </a>

  <nav>
    <ul>
      <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Басты бет</a></li>
      <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">Клуб туралы</a></li>
      <li><a href="events.php" class="<?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">Іс-шаралар</a></li>
      <li><a href="tournaments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tournaments.php' ? 'active' : '' ?>">President CUP's</a></li>
      <li><a href="achievements.php" class="<?= basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'active' : '' ?>">Жетістіктер</a></li>
      <li><a href="news.php" class="<?= basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : '' ?>">Жаңалықтар</a></li>
      <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Байланыс</a></li>
    </ul>
  </nav>

  <div class="header-btn">
    <a class="join-btn" href="index.php#apply">Қосылу</a>
    <a class="join-btn" href="admin/login.php">Admin panel</a>
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