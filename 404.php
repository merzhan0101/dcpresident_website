<?php $pageTitle = 'Бет табылмады'; $pageDescription = 'Сұралған бет табылмады немесе жойылған.'; ?>
<?php include 'blocks/head.php'; ?>

<body>
  <?php include 'blocks/header.php'; ?>

<main>
  <section class="page-header">
    <div class="container">
      <h1>404</h1>
      <p>Кешіріңіз, сұралған бет табылмады</p>
    </div>
  </section>

  <section style="padding: 80px 0; text-align: center;">
    <div class="container">
      <div style="font-size: 100px; line-height: 1; margin-bottom: 20px;">🔍</div>
      <h2 style="margin-bottom: 15px;">Бұл бет жоқ немесе жойылған</h2>
      <p style="color: #888; margin-bottom: 35px; font-size: 18px;">
        Сілтеме қате болуы немесе мазмұн жойылған болуы мүмкін.
      </p>
      <a href="index.php" class="btn btn-large" style="display: inline-block;">← Басты бетке оралу</a>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>
</body>
</html>