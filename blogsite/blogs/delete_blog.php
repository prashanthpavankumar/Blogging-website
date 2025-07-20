<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$blog_id = $_GET['id'] ?? null;

if ($blog_id) {
    // Only allow deleting own blog
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
}

header("Location: ../index.php");
exit;

