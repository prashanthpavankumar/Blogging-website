<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("Blog ID not specified.");
}

$blog_id = $_GET['id'];

// Fetch blog post and author
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

// Fetch like & dislike count (bootstrap version uses your counting loop)
$likes = $conn->prepare("SELECT type, COUNT(*) as count FROM likes WHERE blog_id = ? GROUP BY type");
$likes->bind_param("i", $blog_id);
$likes->execute();
$results = $likes->get_result();

$like_count = 0;
$dislike_count = 0;
while ($row = $results->fetch_assoc()) {
    if ($row['type'] === 'like') $like_count = $row['count'];
    if ($row['type'] === 'dislike') $dislike_count = $row['count'];
}
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
        <a class="btn btn-outline-light ms-auto" href="../index.php">&larr; Home</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="card-title text-primary mb-3"><?= htmlspecialchars($blog['title']) ?></h1>
                    <div class="text-muted mb-3">
                        By <strong><?= htmlspecialchars($blog['username']) ?></strong> &middot; <?= htmlspecialchars($blog['created_at']) ?>
                    </div>
                    <?php if ($blog['image_url']): ?>
                        <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="img-fluid rounded mb-3" alt="Blog image">
                    <?php endif; ?>
                    <div class="mb-4" style="white-space: pre-line;">
                        <?= nl2br(htmlspecialchars($blog['content'])) ?>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <form action="react.php" method="post" class="d-inline">
                            <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                            <input type="hidden" name="type" value="like">
                            <button type="submit" class="btn btn-success btn-sm">
                                👍 Like (<?= $like_count ?>)
                            </button>
                        </form>
                        <form action="react.php" method="post" class="d-inline">
                            <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                            <input type="hidden" name="type" value="dislike">
                            <button type="submit" class="btn btn-danger btn-sm">
                                👎 Dislike (<?= $dislike_count ?>)
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
