<?php
// mark_present.php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("INSERT INTO attendance (user_id, time_in) VALUES (?, NOW())");
$stmt->execute([$user_id]);

header("Location: " . ($_SESSION['role'] === 'member' ? "member_dashboard.php" : "admin_dashboard.php"));
?>
