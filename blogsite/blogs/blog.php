<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("Blog ID not specified.");
}

$blog_id = $_GET['id'];

// Prepare and execute blog query
$stmt = $conn->prepare("SELECT blogs.*, users.username FROM blogs 
                        JOIN users ON blogs.user_id = users.id 
                        WHERE blogs.id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Blog not found.");
}

$blog = $result->fetch_assoc();

// Get likes/dislikes count
$likes_result = $conn->query("SELECT 
    SUM(type = 'like') AS likes, 
    SUM(type = 'dislike') AS dislikes 
    FROM likes WHERE blog_id = $blog_id");
$reaction = $likes_result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blogsite</a>
        <a class="btn btn-outline-light" href="../index.php">&larr; Home</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="card-title text-primary mb-2"><?= htmlspecialchars($blog['title']) ?></h1>
                    <div class="text-muted mb-3">
                        By <b><?= htmlspecialchars($blog['username']) ?></b> • <?= htmlspecialchars($blog['created_at']) ?>
                    </div>
                    <?php if ($blog['image_url']): ?>
                        <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="img-fluid rounded mb-3" alt="Blog Image">
                    <?php endif; ?>
                    <div class="card-text mb-4" style="white-space: pre-line;">
                        <?= nl2br(htmlspecialchars($blog['content'])) ?>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <form action="like.php" method="POST" class="d-inline">
                            <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                            <input type="hidden" name="type" value="like">
                            <button class="btn btn-outline-success" type="submit">
                                👍 Like (<?= $reaction['likes'] ?? 0 ?>)
                            </button>
                        </form>

                        <form action="like.php" method="POST" class="d-inline">
                            <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                            <input type="hidden" name="type" value="dislike">
                            <button class="btn btn-outline-danger" type="submit">
                                👎 Dislike (<?= $reaction['dislikes'] ?? 0 ?>)
                            </button>
                        </form>
                    </div>
                    <a href="../index.php" class="btn btn-secondary">&larr; Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
