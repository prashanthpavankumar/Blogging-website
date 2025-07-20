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
<?php
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
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: auto; padding: 20px; }
        .blog-image { max-width: 100%; height: auto; margin-bottom: 15px; }
        .blog-meta { color: gray; font-size: 0.9em; margin-bottom: 10px; }
        .reaction-buttons form { display: inline-block; margin-right: 10px; }
        .back-link { display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

    <h1><?= htmlspecialchars($blog['title']) ?></h1>
    <div class="blog-meta">
        By <?= htmlspecialchars($blog['username']) ?> on <?= $blog['created_at'] ?>
    </div>

    <?php if ($blog['image_url']): ?>
        <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="blog-image" alt="Blog Image">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($blog['content'])) ?></p>
<div class="reaction-buttons">
<form action="react.php" method="post" style="display:inline;">
    <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
    <input type="hidden" name="type" value="like">
    <button type="submit" class="btn btn-success btn-sm">👍 Like (<?= $like_count ?>)</button>
</form>

<form action="react.php" method="post" style="display:inline;">
    <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
    <input type="hidden" name="type" value="dislike">
    <button type="submit" class="btn btn-danger btn-sm">👎 Dislike (<?= $dislike_count ?>)</button>
</form>
    </div>

    <a href="../index.php" class="back-link">← Back to Home</a>
</body>
</html>
