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
      <h1>Байланыс</h1>
      <p>Бізбен байланысыңыз - біз әрқашан жаңа мүшелер мен серіктестерге қуаныштымыз</p>
    </div>
  </section>

  <!-- Контактная информация -->
  <section class="contact-content">
    <div class="container">
      <div class="contact-grid">
        <!-- Контактные данные -->
        <div class="contact-info">
          <h2>Біздің байланыстар</h2>
          
          <div class="contact-item">
            <div class="contact-icon">📍</div>
            <div class="contact-details">
              <h4>Мекен-жайы</h4>
              <p>Павлодар қ., Ломова, 64 к-сі<br>Торайғыров Университеті</p>
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
              <h4>Әлеуметтік медиа</h4>
              <div class="social-links">
                <a href="https://www.instagram.com/tou_debate_club/" class="social-link instagram" target="_blank">
                    <div class="social-left">
                        <div class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </div>

                        <div class="social-name">
                            Instagram
                        </div>
                    </div>

                    <span class="social-arrow">
                        →
                    </span>
                </a>
                <a href="https://t.me/presidentcup6" class="social-link telegram" target="_blank">
                    <div class="social-left">
                        <div class="social-icon">
                            <i class="fab fa-telegram-plane"></i>
                        </div>

                        <div class="social-name">
                            Telegram
                        </div>
                    </div>

                    <span class="social-arrow">→</span>
                </a>
                <a href="https://wa.me/77019134408" class="social-link whatsapp" target="_blank">
                    <div class="social-left">
                        <div class="social-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>

                        <div class="social-name">
                            WhatsApp
                        </div>
                    </div>

                    <span class="social-arrow">→</span>
                </a>
              </div>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">🕒</div>
            <div class="contact-details">
              <h4>Кездесу уақыты</h4>
              <p>Дс-Ср-Жм, 18:00<br>А-8 аудиториясы, бас ғимарат</p>
            </div>
          </div>
        </div>

        <!-- Форма обратной связи -->
        <div class="contact-form-section">
          <h2>Бізге жазыңыз</h2>
          <form action="submit_contact.php" method="POST" class="contact-form">
            <div class="form-group">
              <label for="name">Сіздің есіміңіз *</label>
              <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
              <label for="email">Email *</label>
              <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон / WhatsApp *</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="+7 (777) 123-45-67"
                    required>
            </div>
            
            <div class="form-group">
              <label for="subject">Хабарлама тақырыбы</label>
              <select id="subject" name="subject">
                <option value="join">Клубқа қосылу</option>
                <option value="partnership">Серіктестік</option>
                <option value="question">Сұрақ</option>
                <option value="other">Басқа</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="message">Хабарлама *</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-large">Хабарлама жіберу</button>
          </form>
        </div>
      </div>

      <!-- Карта -->
      <div class="map-section">
        <h3>Біз осындамыз</h3>
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2441.727119491309!2d76.96400837718654!3d52.2665001549664!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x42f9cb34876c9d7f%3A0xc467658cc4245b1!2z0J3QkNCeICLQotC-0YDQsNC50LPRi9GA0L7QsiDRg9C90LjQstC10YDRgdC40YLQtdGCIg!5e0!3m2!1sru!2skz!4v1761813010501!5m2!1sru!2skz" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <div class="map-placeholder">
            <p>📍 Торайғыров Университеті<br>Павлодар қ., Ломова, 64 к-сі</p>
            <p>Бас ғимарат, 2 қабат, А-8 аудиториясы</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>