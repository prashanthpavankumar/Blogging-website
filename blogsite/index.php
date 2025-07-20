<?php
session_start();
include 'config/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Homepage</title>
</head>
<body>

<h2>Welcome to the Blog</h2>

<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?= $_SESSION['username'] ?> | <a href="auth/logout.php">Logout</a></p>
    <p><a href="blogs/create_blog.php">➕ Create New Blog</a></p>
<?php else: ?>
    <p><a href="auth/login.php">Login</a> | <a href="auth/register.php">Register</a></p>
<?php endif; ?>

<hr>

<?php
// Fetch all blogs
$result = $conn->query("SELECT blogs.*, users.username FROM blogs JOIN users ON blogs.user_id = users.id ORDER BY blogs.created_at DESC");

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
    <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><i>By <?= htmlspecialchars($row['username']) ?> on <?= $row['created_at'] ?></i></p>
        <p><?= nl2br(htmlspecialchars(substr($row['content'], 0, 150))) ?>...</p>
        <a href="blogs/blog.php?id=<?= $row['id'] ?>">Read More</a>
    </div>
<?php
    endwhile;
else:
    echo "<p>No blogs found.</p>";
endif;
?>

</body>
</html>
<?php
session_start();
include 'config/db.php';
?>



