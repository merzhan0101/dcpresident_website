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
      <h1>Біздің клуб туралы</h1>
      <p>DC President - Торайғыров университетінің ең белсенді пікірсайыс клубы туралы көбірек біліңіз</p>
    </div>
  </section>

  <!-- Основная информация -->
  <section class="about-content">
    <div class="container">
      <div class="about-hero">
        <div class="about-hero-text">
          <h2>DC President</h2>
          <p class="lead">DC President — 2019 жылы негізі қаланған Торайғыров университетінің студенттік пікірсайыс клубы.</p>
          <p>Біз үшін пікірсайыс — тек жарыс емес. Бұл өз ойыңды қалыптастыруға, оны дәлелдеуге және өзгелердің көзқарасын түсінуге мүмкіндік беретін орта.</p>
          <p>Клубта студенттер пікірсайыс арқылы сыни ойлауын дамытып, көпшілік алдында сөйлеу дағдысын жетілдіреді және командамен жұмыс істеуді үйренеді.</p>
          <p>Біздің мақсатымыз — өз ойын еркін жеткізетін, дәлелмен сөйлейтін және әртүрлі көзқарасқа құрметпен қарайтын студенттер қауымдастығын қалыптастыру.</p>
        </div>
        <div class="about-hero-image">
          <img src="images/all_members.jpg" alt="Участники дебатного клуба President">
        </div>
      </div>

      <!-- Наши ценности -->
      <div class="values-section">
        <h3>Біздің құндылықтар</h3>
        <div class="values-grid">
          <div class="value-card">
            <div class="value-icon">💬</div>
            <h4>Еркін ой</h4>
            <p>Әр адамның өз көзқарасы бар. Біз өз пікіріңді еркін білдіруге және оны дәлелдеуге мүмкіндік береміз.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🤝</div>
            <h4>Өзара құрмет</h4>
            <p>Пікірлер әртүрлі болуы мүмкін. Біз қарсы көзқарасты тыңдап, пікірталаста бір-бірімізді құрметтеуді үйренеміз.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🧠</div>
            <h4>Сыни ойлау</h4>
            <p>Ақпаратты талдаймыз, сұрақ қоямыз және кез келген пікірге дәлел тұрғысынан қараймыз.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">🚀</div>
            <h4>Үздіксіз даму</h4>
            <p>Әр пікірсайыс — жаңа тәжірибе. Қателесуден қорықпай, әр кездесуден кейін өзімізді жақсартамыз.</p>
          </div>
        </div>
      </div>

      <!-- Наша команда -->
      <div class="team-section">
        <h3>Клуб басшылығы</h3>
        <div class="team-grid">
          <?php 
          $leadership = getClubLeadership();
          if (empty($leadership)): ?>
            <p class="no-members">Басшылық туралы ақпарат жақында пайда болады.</p>
          <?php else: ?>
            <?php foreach ($leadership as $member): ?>
            <div class="team-member">
              <img src="<?= $member['image_path'] ?: 'images/avatar-default.jpg' ?>" 
                   alt="<?= htmlspecialchars($member['full_name']) ?>">
              <h4><?= htmlspecialchars($member['full_name']) ?></h4>
              <p class="position"><?= ucfirst($member['role']) ?></p>
              <p class="faculty"><?= htmlspecialchars($member['faculty']) ?></p>
              <p class="generation">Буын: <?= $member['generation'] ?></p>
              <?php if ($member['birth_day'] && $member['birth_month']): ?>
                <p class="birthday">🎂 <?= $member['birth_day'] ?> <?= getRussianMonthName($member['birth_month']) ?></p>
              <?php endif; ?>
              <?php if ($member['bio']): ?>
                <p class="bio"><?= htmlspecialchars($member['bio']) ?></p>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="text-center" style="margin-top: 40px;">
            <a href="members.php" class="text-main-color">Барлық қатысушыларға өту</a>
        </div>
      </div>

        <!-- Статистика динамическая -->
        <div class="stats-section">
            <h3>Сандар сөйлесін</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?= count(getAllActiveMembers()) ?>+</div>
                    <div class="stat-label">Қатысушылар</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Турнирлер өткізілді</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Турнирлердегі жеңістер</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Жұмыс жылы</div>
                </div>
            </div>
        </div>

    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>

<script src="js/script.js"></script>