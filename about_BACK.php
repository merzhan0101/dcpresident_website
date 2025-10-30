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
      <h1>О нашем клубе</h1>
      <p>Узнайте больше о DC President - самом активном дебатном клубе Торайгыров Университета</p>
    </div>
  </section>

  <!-- Основная информация -->
  <section class="about-content">
    <div class="container">
      <div class="about-hero">
        <div class="about-hero-text">
          <h2>DC President</h2>
          <p class="lead">Мы — студенческий дебатный клуб, объединяющий самых активных, умных и амбициозных студентов Торайгыров Университета.</p>
          <p>Основанный в 2018 году, наш клуб стал площадкой для развития критического мышления, ораторского мастерства и лидерских качеств. Мы верим, что каждый студент может стать уверенным спикером и эффективным коммуникатором.</p>
        </div>
        <div class="about-hero-image">
          <img src="images/about-hero.jpg" alt="Участники дебатного клуба President">
        </div>
      </div>

      <!-- Наши ценности -->
      <div class="values-section">
        <h3>Наши ценности</h3>
        <div class="values-grid">
          <div class="value-card">
            <div class="value-icon">💬</div>
            <h4>Свобода слова</h4>
            <p>Мы создаем безопасное пространство для выражения любых мнений и идей</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🤝</div>
            <h4>Уважение</h4>
            <p>Уважаем противоположные точки зрения и учимся у каждого диалога</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🚀</div>
            <h4>Развитие</h4>
            <p>Постоянно совершенствуем свои навыки и помогаем расти другим</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🏆</div>
            <h4>Лидерство</h4>
            <p>Воспитываем будущих лидеров, способных менять мир к лучшему</p>
          </div>
        </div>
      </div>

      <!-- Наша команда -->
      <div class="team-section">
        <h3>Руководство клуба</h3>
        <div class="team-grid">
          <div class="team-member">
            <img src="images/team1.jpg" alt="Президент клуба">
            <h4>Айгерім Қасымова</h4>
            <p class="position">Президент клуба</p>
            <p class="bio">Студентка 3 курса факультета международных отношений</p>
          </div>
          <div class="team-member">
            <img src="images/team2.jpg" alt="Вице-президент">
            <h4>Қайрат Жүнісов</h4>
            <p class="position">Вице-президент</p>
            <p class="bio">Студент 2 курса факультета журналистики</p>
          </div>
          <div class="team-member">
            <img src="images/team3.jpg" alt="Тренер">
            <h4>Алия Әбдірова</h4>
            <p class="position">Главный тренер</p>
            <p class="bio">Чемпионка республиканских дебатов 2023</p>
          </div>
        </div>
      </div>

      <!-- Статистика -->
      <div class="stats-section">
        <h3>Мы в цифрах</h3>
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">150+</div>
            <div class="stat-label">Участников</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">25+</div>
            <div class="stat-label">Турниров проведено</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">15+</div>
            <div class="stat-label">Побед в турнирах</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">3</div>
            <div class="stat-label">Года работы</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>