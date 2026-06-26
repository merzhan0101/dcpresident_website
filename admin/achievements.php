<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_content = trim($_POST['full_content']);
            $tournament_name = trim($_POST['tournament_name']);
            $position = trim($_POST['position']);
            $achievement_date = $_POST['achievement_date'];

            $image_path = null;

            if (isset($_FILES['achievement_image']) && $_FILES['achievement_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadAchievementPhoto($_FILES['achievement_image']);
            }

            $sql = "INSERT INTO achievements 
                    (title, description, full_content, tournament_name, position, achievement_date, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date, $image_path]);

            // $sql = "INSERT INTO achievements (title, description, full_content, tournament_name, position, achievement_date) 
            //         VALUES (?, ?, ?, ?, ?, ?)";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date]);
            
            header("Location: achievements.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_content = trim($_POST['full_content']);
            $tournament_name = trim($_POST['tournament_name']);
            $position = trim($_POST['position']);
            $achievement_date = $_POST['achievement_date'];

            $image_path = $_POST['current_image'] ?? null;

            if (isset($_FILES['achievement_image']) && $_FILES['achievement_image']['error'] === UPLOAD_ERR_OK) {
                if (
                    $image_path &&
                    strpos($image_path, 'images/achievements/') === 0 &&
                    file_exists('../' . $image_path)
                ) {
                    unlink('../' . $image_path);    
                }

                $image_path = uploadAchievementPhoto($_FILES['achievement_image']);
            }

            $sql = "UPDATE achievements 
                    SET title=?, description=?, full_content=?, tournament_name=?, position=?, achievement_date=?, image_path=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date, $image_path, $id]);
            
            // $sql = "UPDATE achievements SET title=?, description=?, full_content=?, tournament_name=?, position=?, achievement_date=? WHERE id=?";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date, $id]);
            
            header("Location: achievements.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM achievements WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: achievements.php?success=deleted");
            exit;
    }
}

$achievements = getAllAchievements();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление достижениями - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <style>
        .achievements-toolbar{
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

        .achievements-toolbar input,
        .achievements-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .achievements-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .achievements-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .achievements-toolbar input{
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
                <h1>Управление достижениями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить достижение</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Достижение успешно <?= $_GET['success'] == 'added' ? 'добавлено' : ($_GET['success'] == 'updated' ? 'обновлено' : 'удалено') ?>
                </div>
            <?php endif; ?>

            <div class="achievements-toolbar">
                <div class="toolbar-left">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >
                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($achievements) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Турнир</th>
                            <th>Позиция</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achievements as $index=>$achievement): ?>
                        <tr
                            class="achievement-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($achievement['title'])) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($achievement['title']) ?></td>
                            <td><?= htmlspecialchars($achievement['tournament_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($achievement['position'] ?? '-') ?></td>
                            <td><?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></td>
                            <td>
                                <button onclick="openModal('edit', <?= $achievement['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $achievement['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для достижений -->
    <div id="achievementModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить достижение</h2>
            <form method="POST" id="achievementForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="achievementId">
                <input type="hidden" name="current_image" id="currentImage">

                <div id="currentImageContainer" style="display: none; margin-bottom: 15px;">
                    <img id="currentImagePreview"
                        src=""
                        alt="Текущее фото"
                        style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Фото достижения</label>
                    <input type="file" name="achievement_image" id="achievementImage" accept="image/*">
                </div>
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Название достижения *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Турнир</label>
                        <input type="text" name="tournament_name" id="tournament_name">
                    </div>
                    
                    <div class="form-group">
                        <label>Позиция/награда</label>
                        <input type="text" name="position" id="position" placeholder="1 место, Лучший спикер">
                    </div>
                    
                    <div class="form-group">
                        <label>Дата достижения *</label>
                        <input type="date" name="achievement_date" id="achievement_date" required>
                    </div>
                    
                    <div class="form-group"style="grid-column: 1 / -1;">
                        <label>Краткое описание *</label>
                        <textarea name="description" id="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Полное описание</label>
                        <textarea name="full_content" id="full_content" rows="5"></textarea>
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
    
    <script>
        function openModal(action, id = null) {
            const modal = document.getElementById('achievementModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Добавить достижение';
                formAction.value = 'add';
                document.getElementById('achievementForm').reset();
                document.getElementById('achievement_date').valueAsDate = new Date();
            } else {
                title.textContent = 'Редактировать достижение';
                formAction.value = 'edit';
                loadAchievementData(id);
            }
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('achievementModal').style.display = 'none';
        }
        
        function loadAchievementData(id) {
            fetch(`../api/get_achievement.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const currentImage = document.getElementById('currentImage');
                    const currentImageContainer = document.getElementById('currentImageContainer');
                    const currentImagePreview = document.getElementById('currentImagePreview');

                    if (data.image_path) {
                        currentImage.value = data.image_path;
                        currentImagePreview.src = '../' + data.image_path;
                        currentImageContainer.style.display = 'block';
                    } else {
                        currentImage.value = '';
                        currentImagePreview.src = '';
                        currentImageContainer.style.display = 'none';
                    }

                    document.getElementById('achievementId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('tournament_name').value = data.tournament_name || '';
                    document.getElementById('position').value = data.position || '';
                    document.getElementById('achievement_date').value = data.achievement_date;
                    document.getElementById('description').value = data.description;
                    document.getElementById('full_content').value = data.full_content || '';
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Вы уверены, что хотите удалить это достижение?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // FILTER
        const searchInput=document.getElementById("searchInput");
        const rows=document.querySelectorAll(".achievement-row");

        function filterAchievements(){

            const search=searchInput.value.toLowerCase().trim();

            rows.forEach(row=>{

                const title=row.dataset.title;

                const searchOk=
                    title.includes(search);

                row.style.display=
                    searchOk 
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterAchievements);

        // ===============================================================
        window.onclick = function(event) {
            const modal = document.getElementById('achievementModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>