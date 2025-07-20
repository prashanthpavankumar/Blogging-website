<?php
session_start();
include 'config/db.php';

// Fetch blogs with user info
$sql = "SELECT blogs.*, users.username FROM blogs 
        JOIN users ON blogs.user_id = users.id 
        ORDER BY blogs.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BlogSite - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">BlogSite</a>
    <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blogs/create_blog.php">New Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="auth/logout.php">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="auth/login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="auth/register.php">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">Recent Blogs</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                    <p class="text-muted mb-2">By <strong><?= htmlspecialchars($row['username']) ?></strong> on <?= $row['created_at'] ?></p>
                    
                    <?php if (!empty($row['image_url'])): ?>
                        <img src="<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid rounded mb-3" alt="Blog Image">
                    <?php endif; ?>

                    <p class="card-text"><?= nl2br(htmlspecialchars(substr($row['content'], 0, 200))) ?>...</p>

                    <a href="blogs/view_blog.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Read More</a>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                        <a href="blogs/edit_blog.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <a href="blogs/delete_blog.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this blog?');">🗑️ Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No blogs found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
