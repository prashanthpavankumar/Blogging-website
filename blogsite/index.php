<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Blogsite</a>
        <div class="d-flex">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="navbar-text me-3">
                Hello, <b><?= htmlspecialchars($_SESSION['username']) ?></b>
            </span>
            <a class="btn btn-outline-light me-2" href="blogs/create_blog.php">+ New Blog</a>
            <a class="btn btn-light" href="auth/logout.php">Logout</a>
        <?php else: ?>
            <a class="btn btn-outline-light me-2" href="auth/login.php">Login</a>
            <a class="btn btn-warning" href="auth/register.php">Register</a>
        <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container my-4">

    <h1 class="mb-4 text-primary">Recent Blogs</h1>

    <?php
    $result = $conn->query("SELECT blogs.*, users.username FROM blogs JOIN users ON blogs.user_id = users.id ORDER BY blogs.created_at DESC");
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="card text-dark mb-4">
            <div class="card-header bg-info bg-opacity-25">
                <strong><?= htmlspecialchars($row['title']) ?></strong>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">
                    By <b><?= htmlspecialchars($row['username']) ?></b> &middot; <?= htmlspecialchars($row['created_at']) ?>
                </h6>
                <p class="card-text"><?= nl2br(htmlspecialchars(mb_substr($row['content'], 0, 200))) ?>...</p>
                <a href="blogs/blog.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Read More</a>
            </div>
        </div>
    <?php endwhile; else: ?>
        <div class="alert alert-info">No blogs found.</div>
    <?php endif; ?>

</div>
<footer class="bg-primary text-white text-center py-3">
    &copy; <?= date('Y') ?> Blogsite. Powered by PHP & MySQL.
</footer>
</body>
</html>
