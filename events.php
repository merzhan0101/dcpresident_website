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
      <h1>Мероприятия</h1>
      <p>Все предстоящие и прошедшие события нашего клуба</p>
    </div>
  </section>

  <section class="events-page">
    <div class="container">
      <div class="tabs">
        <button class="tab-btn active" data-tab="upcoming">Предстоящие</button>
        <button class="tab-btn" data-tab="past">Прошедшие</button>
      </div>
      
      <!-- Предстоящие мероприятия -->
      <div class="events-grid-page" id="upcoming-events-page">
        <?php 
        $events = getEvents(12, 'upcoming');
        if (empty($events)): ?>
          <div class="no-events">
            <p>На данный момент нет предстоящих мероприятий</p>
            <p>Следите за обновлениями в наших социальных сетях</p>
          </div>
        <?php else: ?>
          <?php foreach ($events as $event): ?>
          <div class="event-card-page">
            <div class="event-image">
              <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                   alt="<?= htmlspecialchars($event['title']) ?>">
            </div>
            <div class="event-info">
              <h3><?= htmlspecialchars($event['title']) ?></h3>
              <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
              <div class="event-meta">
                <span class="event-date">
                  📅 <?= date('d.m.Y', strtotime($event['event_date'])) ?>
                  <?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?>
                </span>
                <span class="tag <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <!-- Прошедшие мероприятия -->
      <div class="events-grid-page" id="past-events-page" style="display: none;">
        <?php 
        $pastEvents = getEvents(12, 'past');
        if (empty($pastEvents)): ?>
          <div class="no-events">
            <p>Пока нет прошедших мероприятий</p>
          </div>
        <?php else: ?>
          <?php foreach ($pastEvents as $event): ?>
          <div class="event-card-page">
            <div class="event-image">
              <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                   alt="<?= htmlspecialchars($event['title']) ?>">
            </div>
            <div class="event-info">
              <h3><?= htmlspecialchars($event['title']) ?></h3>
              <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
              <div class="event-meta">
                <span class="event-date">
                  📅 <?= date('d.m.Y', strtotime($event['event_date'])) ?>
                </span>
                <span class="tag completed">Завершено</span>
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
    const tabBtns = document.querySelectorAll('.tab-btn');
    const upcomingEvents = document.getElementById('upcoming-events-page');
    const pastEvents = document.getElementById('past-events-page');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (this.dataset.tab === 'upcoming') {
                upcomingEvents.style.display = 'grid';
                pastEvents.style.display = 'none';
            } else {
                upcomingEvents.style.display = 'none';
                pastEvents.style.display = 'grid';
            }
        });
    });
});
</script>