<?php
session_start();
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to react.");
}

// Validate input
if (!isset($_POST['blog_id'], $_POST['type'])) {
    die("Invalid request.");
}

$blog_id = (int) $_POST['blog_id'];
$type = $_POST['type'];
$user_id = $_SESSION['user_id'];

if (!in_array($type, ['like', 'dislike'])) {
    die("Invalid reaction type.");
}

// Check if user has already reacted
$stmt = $conn->prepare("SELECT id, type FROM likes WHERE blog_id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing reaction
    $row = $result->fetch_assoc();
    if ($row['type'] === $type) {
        // Same reaction clicked again — remove it
        $delete = $conn->prepare("DELETE FROM likes WHERE id = ?");
        $delete->bind_param("i", $row['id']);
        $delete->execute();
    } else {
        // Change reaction
        $update = $conn->prepare("UPDATE likes SET type = ? WHERE id = ?");
        $update->bind_param("si", $type, $row['id']);
        $update->execute();
    }
} else {
    // New reaction
    $insert = $conn->prepare("INSERT INTO likes (user_id, blog_id, type) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $user_id, $blog_id, $type);
    $insert->execute();
}

// Redirect back to blog page
header("Location: blog.php?id=" . $blog_id);
exit;

