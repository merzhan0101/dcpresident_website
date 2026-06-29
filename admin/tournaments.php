<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $image_path = null;
            if (isset($_FILES['tournament_image']) && $_FILES['tournament_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadTournamentPhoto($_FILES['tournament_image']);
            }

            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_description = trim($_POST['full_description']);
            $level = $_POST['level'];
            $season = trim($_POST['season']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'] ?: null;
            $registration_deadline = $_POST['registration_deadline'] ?: null;
            $prize_fund = $_POST['prize_fund'] ?: null;
            $status = $_POST['status'];
            $organizers = trim($_POST['organizers']);
            $judges = trim($_POST['judges']);
            $participants_count = $_POST['participants_count'] ?: null;
            $registration_link = trim($_POST['registration_link']);
            $registration_type = $_POST['registration_type'] ?? 'telegram';
            $google_form_link = trim($_POST['google_form_link'] ?? '');

            $teaser_video = null;
            if (
                isset($_FILES['teaser_video']) &&
                $_FILES['teaser_video']['error'] === UPLOAD_ERR_OK
            ) {
                $teaser_video = uploadTournamentVideo($_FILES['teaser_video']);
            }
            
            $sql = "INSERT INTO tournaments 
            (title, description, full_description, level, season, start_date, end_date, registration_deadline, prize_fund, status, organizers, judges, participants_count, registration_link, registration_type, google_form_link, image_path, teaser_video) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $description,
                $full_description,
                $level,
                $season,
                $start_date,
                $end_date,
                $registration_deadline,
                $prize_fund,
                $status,
                $organizers,
                $judges,
                $participants_count,
                $registration_link,
                $registration_type,
                $google_form_link,
                $image_path,
                $teaser_video
            ]);
            
            header("Location: tournaments.php?success=added");
            exit;
            
        case 'edit':
            // upload/edit img
            $image_path = $_POST['current_image'] ?? null;
            if (isset($_FILES['tournament_image']) && $_FILES['tournament_image']['error'] === UPLOAD_ERR_OK) {
                if (
                    $image_path &&
                    strpos($image_path, 'images/tournaments/') === 0 &&
                    file_exists('../' . $image_path)
                ) {
                    unlink('../' . $image_path);
                }

                $image_path = uploadTournamentPhoto($_FILES['tournament_image']);
            }

            // ТИЗЕР
            $teaser_video = $_POST['current_teaser_video'] ?? null;
            if (
                isset($_FILES['teaser_video']) &&
                $_FILES['teaser_video']['error'] === UPLOAD_ERR_OK
            ) {
                if (
                    $teaser_video &&
                    strpos($teaser_video, 'videos/tournaments/') === 0 &&
                    file_exists('../' . $teaser_video)
                ) {
                    unlink('../' . $teaser_video);
                }

                $teaser_video = uploadTournamentVideo($_FILES['teaser_video']);
            }

            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_description = trim($_POST['full_description']);
            $level = $_POST['level'];
            $season = trim($_POST['season']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'] ?: null;
            $registration_deadline = $_POST['registration_deadline'] ?: null;
            $prize_fund = $_POST['prize_fund'] ?: null;
            $status = $_POST['status'];
            $organizers = trim($_POST['organizers']);
            $judges = trim($_POST['judges']);
            $participants_count = $_POST['participants_count'] ?: null;
            $registration_link = trim($_POST['registration_link']);
            $registration_type = $_POST['registration_type'] ?? 'telegram';
            $google_form_link = trim($_POST['google_form_link'] ?? '');
            
            $sql = "UPDATE tournaments 
                    SET title=?, description=?, full_description=?, level=?, season=?, start_date=?, end_date=?, registration_deadline=?, prize_fund=?, status=?, 
                    organizers=?, judges=?, participants_count=?, registration_link=?, registration_type=?, google_form_link=?, image_path=?, teaser_video=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $description,
                $full_description,
                $level,
                $season,
                $start_date,
                $end_date,
                $registration_deadline,
                $prize_fund,
                $status,
                $organizers,
                $judges,
                $participants_count,
                $registration_link,
                $registration_type,
                $google_form_link,
                $image_path,
                $teaser_video,
                $id
            ]);
            header("Location: tournaments.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM tournaments WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: tournaments.php?success=deleted");
            exit;
    }
}

$tournaments = getAllTournaments();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление турнирами - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-жоспарланған { background: #ffa726; color: white; }
        .status-тіркеу { background: #4caf50; color: white; }
        .status-процесте { background: #2196f3; color: white; }
        .status-аяқталды { background: #9e9e9e; color: white; }
        .status-жойылды { background: #f44336; color: white; }
        
        .level-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }
        .level-республиканский { background: #b50000; }
        .level-областной { background: #e91e63; }
        .level-городской { background: #9c27b0; }
        .level-школьный { background: #3f51b5; }

        /* filter */
        .tournaments-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            margin:20px 0;
            padding:18px;
            background:var(--gray);
            border:1px solid #333;
            border-radius:12px;
        }

        .toolbar-left{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .tournaments-toolbar input,
        .tournaments-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .tournaments-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .tournaments-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .tournaments-toolbar input{
                min-width:100%;
            }

        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Управление турнирами</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Создать турнир</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Турнир успешно <?= $_GET['success'] == 'added' ? 'создан' : ($_GET['success'] == 'updated' ? 'обновлен' : 'удален') ?>
                </div>
            <?php endif; ?>

            <div class="tournaments-toolbar">
                <div class="toolbar-left">

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >

                    <select id="levelFilter">
                        <option value="all">Все уровни</option>
                        <option value="мектепшілік">Мектепшілік</option>
                        <option value="қалалық">Қалалық</option>
                        <option value="облыстық">Облыстық</option>
                        <option value="республикалық">Республикалық</option>
                    </select>

                    <select id="statusFilter">
                        <option value="all">Все статусы</option>
                        <option value="жоспарланған">Жоспарланған</option>
                        <option value="тіркеу">Тіркеу</option>
                        <option value="процесте">Процесте</option>
                        <option value="аяқталды">Аяқталды</option>
                        <option value="жойылды">Жойылды</option>
                    </select>

                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($tournaments) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Уровень</th>
                            <th>Даты</th>
                            <th>Статус</th>
                            <th>Призовой фонд</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournaments as $index => $tournament): ?>
                        <tr
                            class="tournament-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($tournament['title'])) ?>"
                            data-level="<?= htmlspecialchars($tournament['level']) ?>"
                            data-status="<?= htmlspecialchars($tournament['status']) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($tournament['title']) ?></td>
                            <td><span class="level-badge level-<?= $tournament['level'] ?>"><?= $tournament['level'] ?></span></td>
                            <td><?= date('d.m.Y', strtotime($tournament['start_date'])) ?></td>
                            <td><span class="status-badge status-<?= $tournament['status'] ?>"><?= $tournament['status'] ?></span></td>
                            <td><?= $tournament['prize_fund'] ? number_format($tournament['prize_fund'], 0, ',', ' ') . ' ₸' : '-' ?></td>
                            <td>
                                <button onclick="openModal('edit', <?= $tournament['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $tournament['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для турниров -->
    <div id="tournamentModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Создать турнир</h2>
            <!-- <form method="POST" id="tournamentForm"> -->
            <form method="POST" id="tournamentForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="tournamentId">
                <input type="hidden" name="current_image" id="currentImage">
                <input type="hidden" name="current_teaser" id="currentTeaser">

                <div id="currentImageContainer" style="display: none; margin-bottom: 15px;">
                    <!-- <p>Текущее фото:</p> -->
                    <img id="currentImagePreview"
                        src=""
                        alt="Текущее фото"
                        style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px;">
                </div>

                <div id="currentVideoContainer" style="display:none">
                    <!-- <label>Текущее видео</label> -->
                    <video
                        id="currentVideoPreview"
                        controls
                        width="300">
                    </video>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Фото турнира</label>
                    <input type="file" name="tournament_image" id="tournamentImage" accept="image/*">
                </div>

                <div class="form-group" style="grid-column:1/-1;">

                    <label>Тизер турнира (MP4)</label>

                    <input
                        type="file"
                        name="teaser_video"
                        accept="video/mp4,video/webm,video/ogg">

                    <input
                        type="hidden"
                        name="current_teaser_video"
                        id="current_teaser_video">

                </div>
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Название турнира *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Уровень *</label>
                        <select name="level" id="level" required>
                            <option value="мектепшілік">Мектепшілік</option>
                            <option value="қалалық">Қалалық</option>
                            <option value="облыстық">Облыстық</option>
                            <option value="республикалық">Республикалық</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Сезон *</label>
                        <input type="text" name="season" id="season" placeholder="2024-2025" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Статус *</label>
                        <select name="status" id="status" required>
                            <option value="жоспарланған">Жоспарланған</option>
                            <option value="тіркеу">Тіркеу</option>
                            <option value="процесте">Процесте</option>
                            <option value="аяқталды">Аяқталды</option>
                            <option value="жойылды">Жойылды</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата начала *</label>
                        <input type="date" name="start_date" id="start_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата окончания</label>
                        <input type="date" name="end_date" id="end_date">
                    </div>
                    
                    <div class="form-group">
                        <label>Дедлайн регистрации</label>
                        <input type="date" name="registration_deadline" id="registration_deadline">
                    </div>
                    
                    <div class="form-group">
                        <label>Призовой фонд (₸)</label>
                        <input type="number" name="prize_fund" id="prize_fund" min="0" step="1000">
                    </div>
                    
                    <div class="form-group">
                        <label>Количество участников</label>
                        <input type="number" name="participants_count" id="participants_count" min="0">
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <div class="form-group">
                            <label>Тип регистрации</label>
                            <select name="registration_type" id="registration_type">
                                <option value="telegram">Telegram бот</option>
                                <option value="google_form">Google форма</option>
                            </select>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Ссылка Telegram бота</label>
                            <input type="url" name="registration_link" id="registration_link" placeholder="https://t.me/BotName">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Ссылка Google формы</label>
                            <input type="url" name="google_form_link" id="google_form_link" placeholder="https://forms.gle/...">
                        </div>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Краткое описание *</label>
                        <textarea name="description" id="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Полное описание</label>
                        <textarea name="full_description" id="full_description" rows="5"></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Организаторы (через запятую)</label>
                        <textarea name="organizers" id="organizers" rows="2" placeholder="Имя Фамилия, Имя Фамилия"></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Судьи (через запятую)</label>
                        <textarea name="judges" id="judges" rows="2" placeholder="Имя Фамилия, Имя Фамилия"></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Отмена</button>
                    <button type="submit" class="btn-admin">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

        <!-- CONFIRM -->
    <div id="deleteModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-icon">🗑️</div>

            <h3>Удалить запись?</h3>

            <p>
                Это действие нельзя будет отменить.
                Запись будет удалена безвозвратно.
            </p>

            <div class="delete-modal-actions">
                <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">
                    Отмена
                </button>

                <button type="button" class="btn-confirm-delete" onclick="submitDeleteForm()">
                    Да, удалить
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function openModal(action, id = null) {
            const modal = document.getElementById('tournamentModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Создать турнир';
                formAction.value = 'add';
                document.getElementById('tournamentForm').reset();
                setTimeout(toggleRegistrationFields, 100);
            } else {
                title.textContent = 'Редактировать турнир';
                formAction.value = 'edit';
                // Загрузка данных турнира
                loadTournamentData(id);
                setTimeout(toggleRegistrationFields, 100);
            }
            
            modal.style.display = 'flex';
            // modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('tournamentModal').style.display = 'none';
        }

        function loadTournamentData(id) {
            fetch(`../api/get_tournament.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tournamentId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('level').value = data.level;
                    document.getElementById('season').value = data.season;
                    document.getElementById('status').value = data.status;
                    document.getElementById('start_date').value = data.start_date;
                    document.getElementById('end_date').value = data.end_date || '';
                    document.getElementById('registration_deadline').value = data.registration_deadline || '';
                    document.getElementById('prize_fund').value = data.prize_fund || '';
                    document.getElementById('participants_count').value = data.participants_count || '';
                    document.getElementById('registration_link').value = data.registration_link || '';
                    document.getElementById('registration_type').value = data.registration_type || 'telegram';
                    document.getElementById('google_form_link').value = data.google_form_link || '';
                    document.getElementById('description').value = data.description;
                    document.getElementById('full_description').value = data.full_description || '';
                    document.getElementById('organizers').value = data.organizers || '';
                    document.getElementById('judges').value = data.judges || '';

                    const currentImage = document.getElementById('currentImage');
                    const currentImageContainer = document.getElementById('currentImageContainer');
                    const currentImagePreview = document.getElementById('currentImagePreview');

                    // тизер
                    document.getElementById('current_teaser_video').value =
                        data.teaser_video || '';
                        if (data.teaser_video){
                            currentVideoContainer.style.display='block';
                            currentVideoPreview.src='../'+data.teaser_video;
                        } else{
                            currentVideoContainer.style.display='none';
                        }

                    if (data.image_path) {
                        currentImage.value = data.image_path;
                        currentImagePreview.src = '../' + data.image_path;
                        currentImageContainer.style.display = 'block';
                    } else {
                        currentImage.value = '';
                        currentImagePreview.src = '';
                        currentImageContainer.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function toggleRegistrationFields() {
            const type = document.getElementById('registration_type').value;
            const telegramField = document.getElementById('registration_link').closest('.form-group');
            const googleField = document.getElementById('google_form_link').closest('.form-group');

            if (type === 'google_form') {
                telegramField.style.display = 'none';
                googleField.style.display = 'block';
            } else {
                telegramField.style.display = 'block';
                googleField.style.display = 'none';
            }
        }

        document.getElementById('registration_type').addEventListener('change', toggleRegistrationFields);
        
        let deleteId = null;

        function confirmDelete(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').classList.remove('active');
        }

        function submitDeleteForm() {
            if (!deleteId) return;

            document.getElementById('deleteId').value = deleteId;
            document.getElementById('deleteForm').submit();
        }

        // FILTER
        const searchInput=document.getElementById("searchInput");
        const levelFilter=document.getElementById("levelFilter");
        const statusFilter=document.getElementById("statusFilter");

        const rows=document.querySelectorAll(".tournament-row");

        function filterTournaments(){

            const search=searchInput.value.toLowerCase().trim();
            const level=levelFilter.value;
            const status=statusFilter.value;

            rows.forEach(row=>{

                const title=row.dataset.title;
                const rowLevel=row.dataset.level;
                const rowStatus=row.dataset.status;

                const searchOk=
                    title.includes(search);

                const levelOk=
                    level==="all" ||
                    rowLevel===level;

                const statusOk=
                    status==="all" ||
                    rowStatus===status;

                row.style.display=
                    searchOk && levelOk && statusOk
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterTournaments);
        levelFilter.addEventListener("change",filterTournaments);
        statusFilter.addEventListener("change",filterTournaments);
        
        window.onclick = function(event) {
            const modal = document.getElementById('tournamentModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>