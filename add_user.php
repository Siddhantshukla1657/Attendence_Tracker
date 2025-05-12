<?php
// add_user.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $password  = trim($_POST['password']);
    $role      = $_POST['role']; // 'admin' or 'member'
    
    // Simple validation
    if ($full_name && $username && $password && in_array($role, ['admin','member'])) {
        // Check for existing username
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already exists.';
        } else {
            // Store password in plain text (no hashing)
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, username, password, role)
                VALUES (?, ?, ?, ?)
            ");
            if ($stmt->execute([$full_name, $username, $password, $role])) {
                $success = 'User added successfully!';
            } else {
                $error = 'Failed to add user.';
            }
        }
    } else {
        $error = 'Please fill in all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add New User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <h1>Add New User</h1>
        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color:green;"><?= htmlspecialchars($success, ENT_QUOTES) ?></p>
        <?php endif; ?>
        <form method="post" action="add_user.php">
            <input type="text" name="full_name" placeholder="Full Name" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <label for="role">Select Role:</label>
            <select name="role" id="role">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
            </select><br><br>
            <button type="submit" class="btn">Add User</button>
        </form>
        <br>
        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
