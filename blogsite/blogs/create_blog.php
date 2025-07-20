<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $image_url = trim($_POST["image_url"]); // optional
    $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($content)) {
        $msg = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, content, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $content, $image_url);
        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit;
        } else {
            $msg = "Failed to post blog.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blogsite</a>
        <div class="d-flex">
            <a class="btn btn-outline-light" href="../index.php">&larr; Home</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
