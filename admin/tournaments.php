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
            
            $sql = "INSERT INTO tournaments (title, description, full_description, level, season, start_date, end_date, registration_deadline, prize_fund, status, organizers, judges, participants_count, registration_link) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_description, $level, $season, $start_date, $end_date, $registration_deadline, $prize_fund, $status, $organizers, $judges, $participants_count, $registration_link]);
            
            header("Location: tournaments.php?success=added");
            exit;
            
        case 'edit':
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
            
            $sql = "UPDATE tournaments SET title=?, description=?, full_description=?, level=?, season=?, start_date=?, end_date=?, registration_deadline=?, prize_fund=?, status=?, organizers=?, judges=?, participants_count=?, registration_link=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_description, $level, $season, $start_date, $end_date, $registration_deadline, $prize_fund, $status, $organizers, $judges, $participants_count, $registration_link, $id]);
            
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

        /* .status-планируется { background: #ffa726; color: white; }
        .status-регистрация { background: #4caf50; color: white; }
        .status-в процессе { background: #2196f3; color: white; }
        .status-завершен { background: #9e9e9e; color: white; }
        .status-отменен { background: #f44336; color: white; } */

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
                        <?php foreach ($tournaments as $tournament): ?>
                        <tr>
                            <td><?= $tournament['id'] ?></td>
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
            <form method="POST" id="tournamentForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="tournamentId">
                
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
                        <label>Ссылка для регистрации</label>
                        <input type="url" name="registration_link" id="registration_link" placeholder="https://t.me/BotName">
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
    
    <script>
        function openModal(action, id = null) {
            const modal = document.getElementById('tournamentModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Создать турнир';
                formAction.value = 'add';
                document.getElementById('tournamentForm').reset();
            } else {
                title.textContent = 'Редактировать турнир';
                formAction.value = 'edit';
                // Загрузка данных турнира
                loadTournamentData(id);
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('tournamentModal').style.display = 'none';
        }
        
        function loadTournamentData(id) {
            // Здесь должна быть AJAX загрузка данных
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
                    document.getElementById('description').value = data.description;
                    document.getElementById('full_description').value = data.full_description || '';
                    document.getElementById('organizers').value = data.organizers || '';
                    document.getElementById('judges').value = data.judges || '';
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Вы уверены, что хотите удалить этот турнир?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('tournamentModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>