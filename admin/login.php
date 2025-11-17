<?php
session_start();
include '../php/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $user = getUserByUsername($username);
    
    if ($user && verifyPassword($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;
        updateLastLogin($user['id']);
        
        if ($user['role'] === 'admin') {
            header("Location: dashboard.php");
            // header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в панель управления - DC President</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg) 0%, var(--gray) 100%);
        }
        
        .login-form {
            background: var(--gray);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--text);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            background: var(--bg);
            border: 1px solid #333;
            border-radius: 6px;
            color: var(--text);
            font-size: 16px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--red);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--red);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-login:hover {
            background: #9b0000;
        }
        
        .error-message {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" class="login-form">
            <h2>🔐 Вход в панель управления</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Войти</button>
            
            <div style="text-align: center; margin-top: 20px; color: #888;">
                <small>Доступ только для администраторов</small>
            </div>
        </form>
    </div>
</body>
</html>