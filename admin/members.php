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
            // $birth_day = $_POST['birth_day'];
            // $birth_month = $_POST['birth_month'];
            $birth_day = !empty($_POST['birth_day']) ? (int)$_POST['birth_day'] : null;
            $birth_month = !empty($_POST['birth_month']) ? (int)$_POST['birth_month'] : null;
            $role = $_POST['role'];
            $bio = trim($_POST['bio']);
            
            // Обработка загрузки фото
            $image_path = null;
            if (isset($_FILES['member_photo']) && $_FILES['member_photo']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadMemberPhoto($_FILES['member_photo']);
            }
            
            $sql = "INSERT INTO members (full_name, generation, faculty, birth_day, birth_month, role, bio, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $generation, $faculty, $birth_day, $birth_month, $role, $bio, $image_path]);
            
            header("Location: members.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $name = trim($_POST['full_name']);
            $generation = $_POST['generation'];
            $faculty = trim($_POST['faculty']);
            // $birth_day = $_POST['birth_day'];
            // $birth_month = $_POST['birth_month'];
            $birth_day = !empty($_POST['birth_day']) ? (int)$_POST['birth_day'] : null;
            $birth_month = !empty($_POST['birth_month']) ? (int)$_POST['birth_month'] : null;
            $role = $_POST['role'];
            $bio = trim($_POST['bio']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Обработка загрузки фото
            $image_path = $_POST['current_photo'] ?? null;
            if (isset($_FILES['member_photo']) && $_FILES['member_photo']['error'] === UPLOAD_ERR_OK) {
                // Удаляем старое фото если оно есть
                /*if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }*/
                if (
                    $image_path &&
                    file_exists('../' . $image_path) &&
                    !in_array($image_path, [
                        'images/man.jpg',
                        'images/woman.jpg',
                        'images/avatar-default.jpg'
                    ])
                ) {
                    unlink('../' . $image_path);
                }
                $image_path = uploadMemberPhoto($_FILES['member_photo']);
            }
            
            $sql = "UPDATE members SET full_name=?, generation=?, faculty=?, birth_day=?, birth_month=?, role=?, bio=?, is_active=?, image_path=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $generation, $faculty, $birth_day, $birth_month, $role, $bio, $is_active, $image_path, $id]);
            
            header("Location: members.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
            
            // Удаляем фото участника если оно есть
            $sql = "SELECT image_path FROM members WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $member = $stmt->fetch();
            
            if ($member && $member['image_path'] && file_exists('../' . $member['image_path'])) {
                unlink('../' . $member['image_path']);
            }
            
            $sql = "DELETE FROM members WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: members.php?success=deleted");
            exit;
    }
}

$members = getAllMembers();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
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
        
        .member-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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
            inset: 0;

            justify-content: center;
            align-items: center;

            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: var(--gray);
            padding: 30px;
            border-radius: 10px;

            width: 90%;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;

            margin: 0;
            box-sizing: border-box;
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
        
        .photo-upload {
            grid-column: 1 / -1;
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .current-photo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #444;
        }
        
        .upload-area {
            flex: 1;
        }
        
        .file-input {
            width: 100%;
            padding: 8px;
            border: 1px dashed #666;
            border-radius: 6px;
            background: rgba(255,255,255,0.05);
        }
        
        .photo-preview {
            margin-top: 10px;
            display: none;
        }
        
        .photo-preview img {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid var(--red);
        }

        /* filter */
        .members-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            margin:20px 0;
            padding:18px;
            background:var(--gray);
            border-radius:12px;
            border:1px solid #2f2f2f;
            font-family: inherit;
        }

        .toolbar-left{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .members-toolbar input,
        .members-toolbar select{
            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;
        }

        .members-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{
            padding:9px 14px;
            border-radius:8px;
            background:rgba(181,0,0,.15);
            color:#ddd;
        }

        @media(max-width:900px){

            .members-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .members-toolbar input{
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
                <h1>Управление участниками</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить участника</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div id="successAlert" style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px; transition: opacity 0.5s ease;">
                    ✅ Участник успешно <?= $_GET['success'] == 'added' ? 'добавлен' : ($_GET['success'] == 'updated' ? 'обновлен' : 'удален') ?>
                </div>
                <script>
                    setTimeout(function () {
                        const alertBox = document.getElementById('successAlert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(function () { alertBox.remove(); }, 500);
                        }
                    }, 3000);
                </script>
            <?php endif; ?>

            <!-- ФИЛЬТРАЦИЯ -->
            <div class="members-toolbar">
                <div class="toolbar-left">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по ФИО, факультету..."
                    >

                    <select id="generationFilter">
                        <option value="all">Все поколения</option>
                        <option value="жас">Жас</option>
                        <option value="орта">Орта</option>
                        <option value="аға">Аға</option>
                    </select>

                    <select id="roleFilter">
                        <option value="all">Все роли</option>
                        <option value="клуб мүшесі">Клуб мүшесі</option>
                        <option value="pr">PR</option>
                        <option value="бас бапкер">Бас бапкер</option>
                        <option value="координатор">Координатор</option>
                        <option value="президент">Президент</option>
                    </select>
                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($members) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>ФИО</th>
                            <th>Поколение</th>
                            <th>Факультет</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($members as $index => $member): ?>
                        <tr
                            class="member-row"
                            data-name="<?= htmlspecialchars(mb_strtolower($member['full_name'])) ?>"
                            data-faculty="<?= htmlspecialchars(mb_strtolower($member['faculty'])) ?>"
                            data-generation="<?= htmlspecialchars($member['generation']) ?>"
                            data-role="<?= htmlspecialchars($member['role']) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if ($member['image_path']): ?>
                                    <img src="../<?= $member['image_path'] ?>" alt="<?= htmlspecialchars($member['full_name']) ?>" class="member-photo">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #444; display: flex; align-items: center; justify-content: center; color: #888; font-size: 12px;">
                                        нет
                                    </div>
                                <?php endif; ?>
                            </td>
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
            <form method="POST" id="memberForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="memberId">
                <input type="hidden" name="current_photo" id="currentPhoto">
                
                <!-- Загрузка фото -->
                <div class="photo-upload">
                    <div id="currentPhotoContainer" style="display: none;">
                        <p>Текущее фото:</p>
                        <img id="currentPhotoPreview" class="current-photo" src="" alt="Текущее фото">
                    </div>
                    <div class="upload-area">
                        <label>Фото участника</label>
                        <input type="file" name="member_photo" id="memberPhoto" class="file-input" accept="image/*">
                        <div class="photo-preview" id="photoPreview">
                            <p>Предпросмотр:</p>
                            <img id="previewImage" src="" alt="Предпросмотр">
                        </div>
                    </div>
                </div>
                
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
    
    <!-- Модальное окно обрезки фото -->
    <div id="cropModal" class="modal" style="z-index: 9999;">
        <div class="modal-content" style="max-width: 600px;">
            <h2>Обрезать фото</h2>
            <p class="form-hint" style="margin-bottom: 12px; color: #ffb74d; font-weight: 600;">⚠️ Проверьте, что в рамку попадает лицо целиком — при необходимости подвиньте рамку мышью перед тем как применить.</p>
            <div style="max-height: 420px; overflow: hidden; background: #000;">
                <img id="cropImage" style="max-width: 100%; display: block;">
            </div>
            <div class="form-actions" style="margin-top: 15px;">
                <button type="button" onclick="cancelCrop()" class="btn-admin" style="background: #666;">Отмена</button>
                <button type="button" onclick="applyCrop()" class="btn-admin">Применить обрезку</button>
            </div>
        </div>
    </div>

    <!-- Форма для удаления -->
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
            const modal = document.getElementById('memberModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            const activeField = document.getElementById('activeField');
            const currentPhotoContainer = document.getElementById('currentPhotoContainer');
            
            if (action === 'add') {
                title.textContent = 'Добавить участника';
                formAction.value = 'add';
                document.getElementById('memberForm').reset();
                activeField.style.display = 'none';
                currentPhotoContainer.style.display = 'none';
                document.getElementById('photoPreview').style.display = 'none';
            } else {
                title.textContent = 'Редактировать участника';
                formAction.value = 'edit';
                activeField.style.display = 'block';
                loadMemberData(id);
            }
            
            modal.style.display = 'flex';
            // modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('memberModal').style.display = 'none';
        }
        
        function loadMemberData(id) {
            // AJAX загрузка данных участника
            fetch(`get_member.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('memberId').value = data.id;
                    document.getElementById('fullName').value = data.full_name;
                    document.getElementById('generation').value = data.generation;
                    document.getElementById('faculty').value = data.faculty;
                    document.getElementById('role').value = data.role;
                    document.getElementById('birthDay').value = data.birth_day || '';
                    document.getElementById('birthMonth').value = data.birth_month || '';
                    document.getElementById('bio').value = data.bio || '';
                    document.getElementById('isActive').checked = data.is_active == 1;
                    
                    // Обработка фото
                    const currentPhotoContainer = document.getElementById('currentPhotoContainer');
                    const currentPhotoPreview = document.getElementById('currentPhotoPreview');
                    const currentPhoto = document.getElementById('currentPhoto');
                    
                    if (data.image_path) {
                        currentPhoto.value = data.image_path;
                        currentPhotoPreview.src = '../' + data.image_path;
                        currentPhotoContainer.style.display = 'block';
                    } else {
                        currentPhotoContainer.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error:', error));
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
        const searchInput=document.getElementById('searchInput');
        const generationFilter=document.getElementById('generationFilter');
        const roleFilter=document.getElementById('roleFilter');

        const rows=document.querySelectorAll('.member-row');

        function filterMembers(){

            const search=searchInput.value.toLowerCase().trim();
            const generation=generationFilter.value;
            const role=roleFilter.value;

            rows.forEach(row=>{

                const name=row.dataset.name;
                const faculty=row.dataset.faculty;
                const rowGeneration=row.dataset.generation;
                const rowRole=row.dataset.role;

                const searchOk=
                    name.includes(search) ||
                    faculty.includes(search);

                const generationOk=
                    generation==="all" ||
                    rowGeneration===generation;

                const roleOk=
                    role==="all" ||
                    rowRole===role;

                row.style.display=
                    searchOk && generationOk && roleOk
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterMembers);
        generationFilter.addEventListener("change",filterMembers);
        roleFilter.addEventListener("change",filterMembers);
        
        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const modal = document.getElementById('memberModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Отключаем отправку формы по Enter в однострочных полях (кроме textarea)
        document.getElementById('memberForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // ============================================================
        // ОБРЕЗКА ФОТО УЧАСТНИКА
        // ============================================================
        let memberCropper = null;

        document.getElementById('memberPhoto').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (evt) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = evt.target.result;
                document.getElementById('cropModal').style.display = 'flex';

                if (memberCropper) {
                    memberCropper.destroy();
                }
                memberCropper = new Cropper(cropImage, {
                    aspectRatio: 1,      // квадрат — хорошо смотрится и в круглом аватаре, и в карточке участника
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    ready: function () {
                        const canvasData = memberCropper.getCanvasData();
                        const cropBoxData = memberCropper.getCropBoxData();
                        memberCropper.setCropBoxData({
                            left: cropBoxData.left,
                            top: canvasData.top,
                            width: cropBoxData.width,
                            height: cropBoxData.width
                        });
                    }
                });
            };
            reader.readAsDataURL(file);
        });

        function cancelCrop() {
            document.getElementById('cropModal').style.display = 'none';
            document.getElementById('memberPhoto').value = '';
            if (memberCropper) {
                memberCropper.destroy();
                memberCropper = null;
            }
        }

        function applyCrop() {
            if (!memberCropper) return;

            memberCropper.getCroppedCanvas({ width: 600, height: 600 }).toBlob(function (blob) {
                const croppedFile = new File([blob], 'member_photo.jpg', { type: 'image/jpeg' });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                document.getElementById('memberPhoto').files = dataTransfer.files;

                // Показываем превью результата в уже существующем блоке предпросмотра
                const previewUrl = URL.createObjectURL(blob);
                document.getElementById('previewImage').src = previewUrl;
                document.getElementById('photoPreview').style.display = 'block';

                document.getElementById('cropModal').style.display = 'none';
                memberCropper.destroy();
                memberCropper = null;
            }, 'image/jpeg', 0.9);
        }
    </script>
</body>
</html>