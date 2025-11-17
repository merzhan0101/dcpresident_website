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
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];
            
            $sql = "INSERT INTO events (title, description, event_date, event_time, status) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status]);
            
            header("Location: events.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];
            
            $sql = "UPDATE events SET title=?, description=?, event_date=?, event_time=?, status=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $id]);
            
            header("Location: events.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM events WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: events.php?success=deleted");
            exit;
    }
}

$events = getAllEvents('all');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление мероприятиями - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Управление мероприятиями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить мероприятие</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Мероприятие успешно <?= $_GET['success'] == 'added' ? 'добавлено' : ($_GET['success'] == 'updated' ? 'обновлено' : 'удалено') ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= $event['id'] ?></td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                            <td><?= $event['event_time'] ? date('H:i', strtotime($event['event_time'])) : '-' ?></td>
                            <td><span class="status-badge status-<?= $event['status'] ?>"><?= $event['status'] ?></span></td>
                            <td>
                                <button onclick="openModal('edit', <?= $event['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $event['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для мероприятий -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить мероприятие</h2>
            <form method="POST" id="eventForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="eventId">
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Название мероприятия *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="event_date" id="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Время</label>
                        <input type="time" name="event_time" id="event_time">
                    </div>
                    
                    <div class="form-group">
                        <label>Статус *</label>
                       <select name="status" id="status" required>
                            <option value="Тіркелу">Тіркелу</option>
                            <option value="Аяқталды">Аяқталды</option>
                            <option value="Орындалуда">Орындалуда</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Описание *</label>
                        <textarea name="description" id="description" rows="4" required></textarea>
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
            const modal = document.getElementById('eventModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Добавить мероприятие';
                formAction.value = 'add';
                document.getElementById('eventForm').reset();
                document.getElementById('event_date').valueAsDate = new Date();
            } else {
                title.textContent = 'Редактировать мероприятие';
                formAction.value = 'edit';
                loadEventData(id);
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }
        
        function loadEventData(id) {
            fetch(`../api/get_event.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('eventId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('event_date').value = data.event_date;
                    document.getElementById('event_time').value = data.event_time || '';
                    document.getElementById('status').value = data.status;
                    document.getElementById('description').value = data.description;
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Вы уверены, что хотите удалить это мероприятие?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>