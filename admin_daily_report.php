<?php
session_start();
include 'config.php';

// Check if user is admin.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$report_date = $_GET['report_date'] ?? null;
$records = [];

if ($report_date) {
    // Retrieve all attendance records for the given date by joining attendance and users tables.
    $stmt = $pdo->prepare("
        SELECT a.*, u.full_name 
        FROM attendance a 
        JOIN users u ON a.user_id = u.user_id 
        WHERE DATE(a.time_in) = ? 
        ORDER BY a.time_in DESC
    ");
    $stmt->execute([$report_date]);
    $records = $stmt->fetchAll();
}
?>
<html>
<head>
    <title>Daily Report for <?= htmlspecialchars($report_date) ?></title>
    <style>
        body {
            background-color: #1c1c1c;
            color: #f5e9a8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1c1c1c;
            color: #f5e9a8;
        }
        table, th, td {
            border: 1px solid #d4af37;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #000;
            color: #d4af37;
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
        a {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>Daily Report for <?= htmlspecialchars($report_date) ?></header>
    <div class="dashboard-container">
        <?php if ($report_date): ?>
            <table>
                <tr>
                    <th>User</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                </tr>
                <?php foreach ($records as $rec): ?>
                <tr>
                    <td><?= htmlspecialchars($rec['full_name']) ?></td>
                    <td><?= date('Y-m-d', strtotime($rec['time_in'])) ?></td>
                    <td><?= date('H:i:s', strtotime($rec['time_in'])) ?></td>
                    <td><?= $rec['time_out'] ? date('H:i:s', strtotime($rec['time_out'])) : '-' ?></td>
                    <td><?= number_format($rec['hours_worked'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No date selected. Please go back and choose a date.</p>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="btn">← Back to Dashboard</a>
    </div>
</body>
</html>
