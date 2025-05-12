<?php
session_start();
include 'config.php';

// Check if user is admin.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$addUserError = $addUserSuccess = '';

// Handle new user addition when form is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $role      = $_POST['role']; // 'member' or 'admin'
    
    // Basic validation.
    if ($full_name && $username && $password && in_array($role, ['admin', 'member'])) {
        // Check if username already exists.
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $addUserError = 'Username already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $username, $hashedPassword, $role])) {
                $addUserSuccess = "User '$username' added successfully!";
            } else {
                $addUserError = 'Failed to add user.';
            }
        }
    } else {
        $addUserError = 'Please fill in all fields correctly.';
    }
}

// Retrieve members list for the report dropdown.
$stmt = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'member'");
$members = $stmt->fetchAll();

// Set today's date to feed into Today's Report
$today = date('Y-m-d');
?>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        /* Overall Page Styles */
        body {
            background-color: #1c1c1c;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f5e9a8;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #000;
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #d4af37;
        }
        .dashboard-container {
            max-width: 1100px;
            margin: 30px auto;
            background: #2c2c2c;
            padding: 20px;
            border-radius: 12px;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .navbar a {
            text-decoration: none;
            color: #d4af37;
            font-size: 18px;
            margin: 0 20px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .card {
            background-color: #1c1c1c;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }
        .btn {
            background-color: rgb(255, 245, 210);
            color: #1c1c1c;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #b2932a;
        }
        input[type="text"], input[type="password"], input[type="date"] {
            padding: 8px;
            margin: 5px 0;
            width: 98%;
            border: 1px solid #d4af37;
            border-radius: 4px;
            background: #1c1c1c;
            color: #f5e9a8;
        }
        select {
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #d4af37;
            background: #1c1c1c;
            color: #f5e9a8;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .error {
            background-color: #ffcccc;
            color: #900;
        }
        .success {
            background-color: #ccffcc;
            color: #060;
        }
        form {
            margin: 0;
        }
    </style>
</head>
<body>
    <header>Admin Dashboard</header>
    <div class="dashboard-container">
        <div class="navbar">
            <div><strong>Welcome, Admin!</strong></div>
            <div>
                <a href="logout.php" class="btn">Logout</a>
            </div>
        </div>
        
        <!-- Add New User Section -->
        <div class="card">
            <h2>Add New User</h2>
            <?php if ($addUserError): ?>
                <div class="message error"><?= htmlspecialchars($addUserError) ?></div>
            <?php endif; ?>
            <?php if ($addUserSuccess): ?>
                <div class="message success"><?= htmlspecialchars($addUserSuccess) ?></div>
            <?php endif; ?>
            <form action="admin_dashboard.php" method="post">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" id="role">
                        <option value="member" selected>Member</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="btn">Add User</button>
            </form>
        </div>
        
        <!-- Member Report Section -->
        <div class="card">
            <h2>View Member Report</h2>
            <form action="admin_view_report.php" method="get">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id">
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['user_id'] ?>"><?= htmlspecialchars($member['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">View Report</button>
            </form>
        </div>

        <!-- Daily Report Section (Select Date) -->
        <div class="card">
            <h2>Daily Report</h2>
            <form action="admin_daily_report.php" method="get">
                <label for="report_date">Select Date:</label>
                <input type="date" id="report_date" name="report_date" required>
                <button type="submit" class="btn">View Daily Report</button>
                <a href="admin_daily_report.php?report_date=<?= $today ?>" class="btn">Today's Report</a>
            </form>
        </div>
    </div>
</body>
</html>
