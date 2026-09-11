<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php $pageTitle = 'Турнирлер'; $pageDescription = 'President дебат клубының турнирлері'; ?>
<?php include 'blocks/head.php'; ?>

<link rel="stylesheet" href="css/tournaments.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Біздің пікірсайыстар</h1>
      <p>Пікірсайыс бойынша республикалық, облыстық, қалалық және мектеп пікірсайыстарын ұйымдастырамыз</p>
    </div>
  </section>

  <section class="tournaments-page">
    <div class="container">
      <!-- Фильтры по уровню -->
      <div class="tournaments-filters">
        <button class="filter-btn active" data-filter="all">Барлық турнирлер</button>
        <button class="filter-btn" data-filter="республикалық">Республикалық</button>
        <button class="filter-btn" data-filter="облыстық">Облыстық</button>
        <button class="filter-btn" data-filter="қалалық">Қалалық</button>
        <button class="filter-btn" data-filter="мектепшілік">Мектепшілік</button>
      </div>

      <!-- Список турниров -->
      <div class="tournaments-grid">
        <?php 
        $tournaments = getAllTournaments();
        if (empty($tournaments)): ?>
          <div class="no-tournaments">
            <p>Қазіргі уақытта жоспарланған турнирлер жоқ</p>
          </div>
        <?php else: ?>
          <?php foreach ($tournaments as $tournament): ?>
          <div class="tournament-card" data-level="<?= $tournament['level'] ?>">
            <div class="tournament-image">
              <img src="<?= $tournament['image_path'] ?: 'images/tournament-default.png' ?>" 
                   alt="<?= htmlspecialchars($tournament['title']) ?>">
              <span class="tournament-status <?= $tournament['status'] ?>"><?= ucfirst($tournament['status']) ?></span>
              <span class="tournament-level <?= $tournament['level'] ?>"><?= ucfirst($tournament['level']) ?></span>
            </div>
            <div class="tournament-info">
              <h3><?= htmlspecialchars($tournament['title']) ?></h3>
              <p class="tournament-season">Сезон: <?= htmlspecialchars($tournament['season']) ?></p>
              <p class="tournament-dates">
                📅 <?= date('d.m.Y', strtotime($tournament['start_date'])) ?>
                <?= $tournament['end_date'] ? ' - ' . date('d.m.Y', strtotime($tournament['end_date'])) : '' ?>
              </p>
              <?php if ($tournament['prize_fund']): ?>
                <p class="tournament-prize">💰 Жүлде қоры: <?= number_format($tournament['prize_fund'], 0, ',', ' ') ?> ₸</p>
              <?php endif; ?>
              <?php if ($tournament['participants_count']): ?>
                <p class="tournament-participants">👥 Қатысушылар саны: <?= $tournament['participants_count'] ?>+</p>
              <?php endif; ?>
              <p class="tournament-description"><?= htmlspecialchars($tournament['description']) ?></p>
              <div class="tournament-actions">
                <a href="tournament-single.php?id=<?= $tournament['id'] ?>" class="btn btn-small">Толығырақ</a>
                <?php if ($tournament['status'] == 'Тіркеу' && $tournament['registration_deadline']): ?>
                    <?php if ($tournament['registration_link']): ?>
                        <a href="<?= $tournament['registration_link'] ?>" class="btn btn-outline btn-small" target="_blank">Тіркелу</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-outline btn-small">Тіркелу</a>
                    <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const tournamentCards = document.querySelectorAll('.tournament-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            tournamentCards.forEach(card => {
                if (filter === 'all' || card.dataset.level === filter) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    const scrollBtn = document.getElementById("scrollTopBtn");

    window.addEventListener("scroll", () => {

        if (window.scrollY > 400) {
            scrollBtn.classList.add("show");
        } else {
            scrollBtn.classList.remove("show");
        }

    });

    scrollBtn.addEventListener("click", () => {

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });

    });
});
</script>
</body>
</html>