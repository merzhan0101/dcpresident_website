<footer>
  <div class="container footer-content">

    <div class="footer-logo">
      <img src="images/logo_president.png" alt="Debate Club President Logo">
      <p>DC President</p>
      <p>Toraighyrov University</p>
    </div>

    <div class="footer-links">
      <h3>Навигация</h3>
      <ul>
        <li><a href="index.php">Басты бет</a></li>
        <li><a href="/about">Клуб туралы</a></li>
        <li><a href="/events">Іс-шаралар</a></li>
        <li><a href="/tournaments">President CUP's</a></li>
        <li><a href="/achievements">Жетістіктер</a></li>
        <li><a href="/news">Жаңалықтар</a></li>
        <li><a href="/members">President-тіктер</a></li>
        <li><a href="/contact">Байланыс</a></li>
      </ul>
    </div>

    <div class="footer-contact">
    <h3>Байланыс</h3>

    <p>
        <i class="fa-solid fa-envelope"></i>
        <strong>Email:</strong>
        <a href="mailto:president.dc@toraighyrov.edu.kz">president.dc@toraighyrov.edu.kz</a>
    </p>

    <p>
        <i class="fa-brands fa-instagram"></i>
        <strong>Instagram:</strong>
        <a href="https://www.instagram.com/tou_debate_club/?igsh=dzd4ZmFjcXVoZjll" target="_blank">@tou_debate_club</a>
    </p>

    <p>
        <i class="fa-brands fa-telegram"></i>
        <strong>Telegram:</strong>
        <a href="https://t.me/presidentcup6" target="_blank">@PresidentCup6</a>
    </p>
    </div>

  </div>

  <div class="footer-bottom">
    <p>© 2025 President Debate Club | Developed by MerJVN</p>
  </div>

  <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
      <p><a href="admin/dashboard.php" style="color: var(--red);">⚙️ Панель управления</a></p>
  <?php else: ?>
      <!-- <p><a class="admin-link" href="admin/login.php">Администратору</a></p> -->
      <!-- <p><a class="join-btn" href="admin/login.php">Администратору</a></p> -->
  <?php endif; ?>
</footer>