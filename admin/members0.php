<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $name = trim($_POST['full_name']);
            $generation = $_POST['generation'];
            $faculty = trim($_POST['faculty']);
            $birth_day = $_POST['birth_day'];
            $birth_month = $_POST['birth_month'];
            $role = $_POST['role'];
            $bio = trim($_POST['bio']);
            
            $sql = "INSERT INTO members (full_name, generation, faculty, birth_day, birth_month, role, bio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $generation, $faculty, $birth_day, $birth_month, $role, $bio]);
            
            header("Location: members.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $name = trim($_POST['full_name']);
            $generation = $_POST['generation'];
            $faculty = trim($_POST['faculty']);
            $birth_day = $_POST['birth_day'];
            $birth_month = $_POST['birth_month'];
            $role = $_POST['role'];
            $bio = trim($_POST['bio']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $sql = "UPDATE members SET full_name=?, generation=?, faculty=?, birth_day=?, birth_month=?, role=?, bio=?, is_active=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $generation, $faculty, $birth_day, $birth_month, $role, $bio, $is_active, $id]);
            
            header("Location: members.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM members WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: members.php?success=deleted");
            exit;
    }
}

$members = getAllActiveMembers();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление участниками - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <style>
        .admin-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-table {
            width: 100%;
            background: var(--gray);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .admin-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        
        .admin-table th {
            background: rgba(181, 0, 0, 0.2);
            color: var(--red);
            font-weight: 600;
        }
        
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
        
        .btn-edit {
            background: #2196f3;
            color: white;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: var(--gray);
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Управление участниками</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить участника</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Участник успешно <?= $_GET['success'] == 'added' ? 'добавлен' : ($_GET['success'] == 'updated' ? 'обновлен' : 'удален') ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Поколение</th>
                            <th>Факультет</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?= $member['id'] ?></td>
                            <td><?= htmlspecialchars($member['full_name']) ?></td>
                            <td><?= $member['generation'] ?></td>
                            <td><?= htmlspecialchars($member['faculty']) ?></td>
                            <td><?= $member['role'] ?></td>
                            <td>
                                <button onclick="openModal('edit', <?= $member['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $member['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для добавления/редактирования -->
    <div id="memberModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить участника</h2>
            <form method="POST" id="memberForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="memberId">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>ФИО *</label>
                        <input type="text" name="full_name" id="fullName" required>
                    </div>
                    <div class="form-group">
                        <label>Поколение *</label>
                        <select name="generation" id="generation" required>
                            <option value="жас">Жас</option>
                            <option value="орта">Орта</option>
                            <option value="аға">Аға</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Факультет *</label>
                        <input type="text" name="faculty" id="faculty" required>
                    </div>
                    <div class="form-group">
                        <label>Роль *</label>
                        <select name="role" id="role" required>
                            <option value="клуб мүшесі">Клуб мүшесі</option>
                            <option value="pr">PR</option>
                            <option value="бас бапкер">Бас бапкер</option>
                            <option value="координатор">Координатор</option>
                            <option value="президент">Президент</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>День рождения</label>
                        <input type="number" name="birth_day" id="birthDay" min="1" max="31">
                    </div>
                    <div class="form-group">
                        <label>Месяц рождения</label>
                        <input type="number" name="birth_month" id="birthMonth" min="1" max="12">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Биография</label>
                        <textarea name="bio" id="bio" rows="3"></textarea>
                    </div>
                    <div class="form-group" id="activeField" style="display: none;">
                        <label>
                            <input type="checkbox" name="is_active" id="isActive" value="1"> Активный
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Отмена</button>
                    <button type="submit" class="btn-admin">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Форма для удаления -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>
    
    <script>
        function openModal(action, id = null) {
            const modal = document.getElementById('memberModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const activeField = document.getElementById('activeField');
            
            if (action === 'add') {
                title.textContent = 'Добавить участника';
                formAction.value = 'add';
                document.getElementById('memberForm').reset();
                activeField.style.display = 'none';
            } else {
                title.textContent = 'Редактировать участника';
                formAction.value = 'edit';
                activeField.style.display = 'block';
                // Загрузка данных участника через AJAX или из data-атрибутов
                loadMemberData(id);
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('memberModal').style.display = 'none';
        }
        
        function loadMemberData(id) {
            // Здесь должна быть AJAX загрузка данных
            // Для простоты можно передать данные через data-атрибуты
        }
        
        function confirmDelete(id) {
            if (confirm('Вы уверены, что хотите удалить этого участника?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const modal = document.getElementById('memberModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>