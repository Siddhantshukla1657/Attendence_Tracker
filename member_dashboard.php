<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve total hours worked.
$stmt = $pdo->prepare("SELECT SUM(hours_worked) as total_hours FROM attendance WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch();
$total_hours = $result['total_hours'] ?? 0;

// Retrieve detailed attendance records.
$stmt = $pdo->prepare("SELECT time_in, time_out, hours_worked FROM attendance WHERE user_id = ? ORDER BY time_in DESC");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();
?>
<html>
<head>
    <title>Member Dashboard</title>
    <!-- You can also include an external CSS file like styles.css if desired -->
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
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }
        .navbar a {
            text-decoration: none;
            color: #d4af37;
            font-size: 18px;
            margin-left: 20px;
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
        h2, h3 {
            margin-top: 0;
        }
        .btn {
            background-color:rgb(255, 251, 240);
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #d4af37;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        form {
            display: inline;
        }
    </style>
</head>
<body>
    <header>Member Dashboard</header>
    <div class="dashboard-container">
        <div class="navbar">
            <a href="logout.php" class="btn">Logout</a>
        </div>
        <div class="card">
            <h2>Attendance Actions</h2>
            <form action="mark_present.php" method="post">
                <button type="submit" class="btn">Mark Present</button>
            </form>
            <form action="mark_leaving.php" method="post">
                <button type="submit" class="btn">Mark Leaving</button>
            </form>
        </div>
        <div class="card">
            <h2>Total Hours Worked: <?= number_format($total_hours, 2) ?> hrs</h2>
        </div>
        <div class="card">
            <h3>Detailed Report</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                </tr>
                <?php foreach ($records as $rec): ?>
                <tr>
                    <td><?= date('Y-m-d', strtotime($rec['time_in'])) ?></td>
                    <td><?= date('H:i:s', strtotime($rec['time_in'])) ?></td>
                    <td><?= $rec['time_out'] ? date('H:i:s', strtotime($rec['time_out'])) : '-' ?></td>
                    <td><?= number_format($rec['hours_worked'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
