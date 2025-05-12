<?php
// login.php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Fetch the stored (plain-text) password
    $stmt = $pdo->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Plain-text comparison instead of password_verify()
    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role'];
        
        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: member_dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <style>
        body {
            background-color: #1c1c1c;
            color: #f5e9a8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login {
            background-color: #2c2c2c;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
            width: 350px;
            text-align: center;
        }
        h1 {
            color: #d4af37;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d4af37;
            border-radius: 4px;
            background-color: #1c1c1c;
            color: #f5e9a8;
        }
        .btn {
            background-color: #d4af37;
            color: #1c1c1c;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #b2932a;
        }
        p {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <p style="color:#ff6b6b;"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
        <?php endif; ?>
        <form method="post" action="index.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
