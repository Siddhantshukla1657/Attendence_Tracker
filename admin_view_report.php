<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    exit('No user selected.');
}

// Fetch total hours for the user.
$stmt = $pdo->prepare("SELECT SUM(hours_worked) as total_hours FROM attendance WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch();
$total_hours = $result['total_hours'] ?? 0;

// Fetch detailed attendance records.
$stmt = $pdo->prepare("SELECT time_in, time_out, hours_worked FROM attendance WHERE user_id = ? ORDER BY time_in DESC");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();

// Fetch the user’s name.
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch();
$username = $userData['full_name'];
?>
<html>
<head>
  <title>Report for <?= htmlspecialchars($username) ?></title>
  <style>
    body {
        background-color: #1c1c1c;
        color: #f5e9a8;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }
    header, h1 {
        background-color: #000;
        padding: 20px;
        color: #d4af37;
        text-align: center;
        margin: 0;
    }
    .report {
        max-width: 900px;
        margin: 30px auto;
        background: #2c2c2c;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
    }
    h2 {
        color: #d4af37;
        margin-bottom: 20px;
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #1c1c1c;
        color: #f5e9a8;
    }
    table th, table td {
        border: 1px solid #d4af37;
        padding: 10px;
        text-align: center;
    }
    table th {
        background-color: #000;
        color: #d4af37;
    }
    a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #d4af37;
        color: #1c1c1c;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
    }
    a:hover {
        background-color: #b2932a;
    }
  </style>
</head>
<body>
  <header>Report for <?= htmlspecialchars($username) ?></header>
  <div class="report">
    <h2>Total Hours Worked: <?= number_format($total_hours, 2) ?> hrs</h2>
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
    <a href="admin_dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
