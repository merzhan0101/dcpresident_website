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

            $image_path = null;
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadEventPhoto($_FILES['event_image']);
            }

            $sql = "INSERT INTO events (title, description, event_date, event_time, status, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $image_path]);

            // $sql = "INSERT INTO events (title, description, event_date, event_time, status) 
            //         VALUES (?, ?, ?, ?, ?)";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $event_date, $event_time, $status]);
            
            header("Location: events.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];

            $image_path = $_POST['current_image'] ?? null;

            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                if (
                    $image_path &&
                    strpos($image_path, 'images/events/') === 0 &&
                    file_exists('../' . $image_path)
                ) {
                    unlink('../' . $image_path);
                }

                $image_path = uploadEventPhoto($_FILES['event_image']);
            }
            
            $sql = "UPDATE events 
                    SET title=?, description=?, event_date=?, event_time=?, status=?, image_path=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $image_path, $id]);

            // $sql = "UPDATE events SET title=?, description=?, event_date=?, event_time=?, status=? WHERE id=?";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $event_date, $event_time, $status, $id]);
            
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
    <style>
        .events-toolbar{
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

        .events-toolbar input,
        .events-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .events-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .events-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .events-toolbar input{
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
                <h1>Управление мероприятиями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить мероприятие</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Мероприятие успешно <?= $_GET['success'] == 'added' ? 'добавлено' : ($_GET['success'] == 'updated' ? 'обновлено' : 'удалено') ?>
                </div>
            <?php endif; ?>

            <div class="events-toolbar">
                <div class="toolbar-left">

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >

                    <select id="statusFilter">
                        <option value="all">Все статусы</option>
                        <option value="Тіркелу">Тіркелу</option>
                        <option value="Орындалуда">Орындалуда</option>
                        <option value="Аяқталды">Аяқталды</option>
                    </select>

                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($events) ?></span>
                </div>
            </div>
            
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
                        <?php foreach ($events as $index=>$event): ?>
                        <tr
                            class="event-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($event['title'])) ?>"
                            data-status="<?= htmlspecialchars($event['status']) ?>"
                        >
                            <td><?= $index + 1 ?></td>
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
            <form method="POST" id="eventForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="eventId">
                <input type="hidden" name="current_image" id="currentImage">

                <div id="currentImageContainer" style="display: none; margin-bottom: 15px;">
                    <img id="currentImagePreview"
                        src=""
                        alt="Текущее фото"
                        style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Фото мероприятия</label>
                    <input type="file" name="event_image" id="eventImage" accept="image/*">
                </div>
                
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
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }
        
        function loadEventData(id) {
            fetch(`../api/get_event.php?id=${id}`)
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

                    document.getElementById('eventId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('event_date').value = data.event_date;
                    document.getElementById('event_time').value = data.event_time || '';
                    document.getElementById('status').value = data.status;
                    document.getElementById('description').value = data.description;
                });
        }
        
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
        const statusFilter=document.getElementById("statusFilter");

        const rows=document.querySelectorAll(".event-row");

        function filterEvents(){

            const search=searchInput.value.toLowerCase().trim();
            const status=statusFilter.value;

            rows.forEach(row=>{

                const title=row.dataset.title;
                const rowStatus=row.dataset.status;

                const searchOk=
                    title.includes(search);

                const statusOk=
                    status==="all" ||
                    rowStatus===status;

                row.style.display=
                    searchOk && statusOk
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterEvents);
        statusFilter.addEventListener("change",filterEvents);
        
        // ============================================================
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>