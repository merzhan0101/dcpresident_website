<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php include 'blocks/head.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <!-- Заголовок страницы -->
  <section class="page-header">
    <div class="container">
      <h1>Контакты</h1>
      <p>Свяжитесь с нами - мы всегда рады новым участникам и партнерам</p>
    </div>
  </section>

  <!-- Контактная информация -->
  <section class="contact-content">
    <div class="container">
      <div class="contact-grid">
        <!-- Контактные данные -->
        <div class="contact-info">
          <h2>Наши контакты</h2>
          
          <div class="contact-item">
            <div class="contact-icon">📍</div>
            <div class="contact-details">
              <h4>Адрес</h4>
              <p>г. Павлодар, ул. Ломова, 64<br>Торайгыров Университет</p>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">📧</div>
            <div class="contact-details">
              <h4>Email</h4>
              <p>president.dc@toraighyrov.edu.kz</p>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">📱</div>
            <div class="contact-details">
              <h4>Социальные сети</h4>
              <div class="social-links">
                <a href="https://www.instagram.com/tou_debate_club/" class="social-link instagram">
                  <span class="social-icon">📷</span>
                  Instagram
                </a>
                <a href="https://t.me/presidentcup6" class="social-link telegram">
                  <span class="social-icon">✈️</span>
                  Telegram
                </a>
                <a href="#" class="social-link whatsapp">
                  <span class="social-icon">💬</span>
                  WhatsApp
                </a>
              </div>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">🕒</div>
            <div class="contact-details">
              <h4>Время встреч</h4>
              <p>Пн-Ср-Пт, 18:00<br>Аудитория А-8, главный корпус</p>
            </div>
          </div>
        </div>

        <!-- Форма обратной связи -->
        <div class="contact-form-section">
          <h2>Напишите нам</h2>
          <form action="submit_contact.php" method="POST" class="contact-form">
            <div class="form-group">
              <label for="name">Ваше имя *</label>
              <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
              <label for="email">Email *</label>
              <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
              <label for="subject">Тема сообщения</label>
              <select id="subject" name="subject">
                <option value="join">Вступление в клуб</option>
                <option value="partnership">Партнерство</option>
                <option value="question">Вопрос</option>
                <option value="other">Другое</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="message">Сообщение *</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-large">Отправить сообщение</button>
          </form>
        </div>
      </div>

      <!-- Карта -->
      <div class="map-section">
        <h3>Мы находимся здесь</h3>
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2441.727119491309!2d76.96400837718654!3d52.2665001549664!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x42f9cb34876c9d7f%3A0xc467658cc4245b1!2z0J3QkNCeICLQotC-0YDQsNC50LPRi9GA0L7QsiDRg9C90LjQstC10YDRgdC40YLQtdGCIg!5e0!3m2!1sru!2skz!4v1761813010501!5m2!1sru!2skz" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <div class="map-placeholder">
            <p>📍 Торайгыров Университет<br>г. Павлодар, ул. Ломова, 64</p>
            <p>Главный корпус, 2 этаж, аудитория А-8</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>