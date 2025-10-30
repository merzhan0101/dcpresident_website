<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php include 'blocks/head.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Наши участники</h1>
      <p>Знакомьтесь с командой DC President - талантливыми студентами, которые делают наш клуб лучше</p>
    </div>
  </section>

  <section class="members-page">
    <div class="container">

    <!-- Ближайшие дни рождения -->
      <div class="birthdays-section">
        <h2>🎉 Ближайшие дни рождения</h2>
        <div class="birthdays-grid">
          <?php 
          $upcomingBirthdays = getUpcomingBirthdays(5);
          if (empty($upcomingBirthdays)): ?>
            <div class="no-birthdays">
              <p>В ближайшее время дней рождения нет</p>
            </div>
          <?php else: ?>
            <?php foreach ($upcomingBirthdays as $member): 
              $daysUntil = daysUntilBirthday($member['birth_month'], $member['birth_day']);
            ?>
            <div class="birthday-card">
              <div class="birthday-avatar">
                <img src="<?= $member['image_path'] ?: ($member['role'] == 'президент' ? 'images/woman.jpg' : 'images/man.jpg') ?>" 
                     alt="<?= htmlspecialchars($member['full_name']) ?>">
                <div class="birthday-badge">🎂</div>
              </div>
              <div class="birthday-info">
                <h4><?= htmlspecialchars($member['full_name']) ?></h4>
                <p class="birthday-date">
                  <?= $member['birth_day'] ?> <?= getRussianMonthName($member['birth_month']) ?>
                </p>
                <p class="birthday-days">
                  <?php if ($daysUntil == 0): ?>
                    <span class="today">🎉 Сегодня!</span>
                  <?php elseif ($daysUntil == 1): ?>
                    <span class="tomorrow">Завтра!</span>
                  <?php else: ?>
                    Через <?= $daysUntil ?> <?= getRussianDaysWord($daysUntil) ?>
                  <?php endif; ?>
                </p>
                <p class="birthday-role"><?= ucfirst($member['role']) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>


      <!-- Фильтры -->
      <div class="members-filters">
        <button class="filter-btn active" data-filter="all">Все</button>
        <button class="filter-btn" data-filter="молодое">Молодое поколение</button>
        <button class="filter-btn" data-filter="среднее">Среднее поколение</button>
        <button class="filter-btn" data-filter="старшее">Старшее поколение</button>
      </div>

      <!-- Список участников -->
      <div class="members-grid">
        <?php 
        $members = getAllActiveMembers();
        if (empty($members)): ?>
          <div class="no-members">
            <p>Пока нет участников</p>
          </div>
        <?php else: ?>
          <?php foreach ($members as $member): ?>
          <div class="member-card" data-generation="<?= $member['generation'] ?>">
            <div class="member-image">
              <img src="<?= $member['image_path'] ?: 'images/avatar-default.jpg' ?>" 
                   alt="<?= htmlspecialchars($member['full_name']) ?>">
              <span class="member-role-badge <?= $member['role'] ?>"><?= ucfirst($member['role']) ?></span>
            </div>
            <div class="member-info">
              <h3><?= htmlspecialchars($member['full_name']) ?></h3>
              <p class="member-faculty">🎓 <?= htmlspecialchars($member['faculty']) ?></p>
              <p class="member-generation">👥 <?= ucfirst($member['generation']) ?> поколение</p>
              <?php if ($member['birth_day'] && $member['birth_month']): ?>
                <p class="member-birthday">🎂 <?= $member['birth_day'] ?> <?= getRussianMonthName($member['birth_month']) ?></p>
              <?php endif; ?>
              <?php if ($member['bio']): ?>
                <p class="member-bio"><?= htmlspecialchars($member['bio']) ?></p>
              <?php endif; ?>
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
    const memberCards = document.querySelectorAll('.member-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Убираем активный класс у всех кнопок
            filterBtns.forEach(b => b.classList.remove('active'));
            // Добавляем активный класс текущей кнопке
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Показываем/скрываем карточки
            memberCards.forEach(card => {
                if (filter === 'all' || card.dataset.generation === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>