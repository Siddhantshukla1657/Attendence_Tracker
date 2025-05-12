<?php
// mark_leaving.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the latest attendance record where time_out is not set
$stmt = $pdo->prepare("SELECT attendance_id, time_in FROM attendance WHERE user_id = ? AND time_out IS NULL ORDER BY time_in DESC LIMIT 1");
$stmt->execute([$user_id]);
$record = $stmt->fetch();

if ($record) {
    $attendance_id = $record['attendance_id'];
    $stmt = $pdo->prepare("UPDATE attendance SET time_out = NOW(), hours_worked = TIMESTAMPDIFF(SECOND, time_in, NOW())/3600 WHERE attendance_id = ?");
    $stmt->execute([$attendance_id]);
}

header("Location: " . ($_SESSION['role'] === 'member' ? "member_dashboard.php" : "admin_dashboard.php"));
?>
