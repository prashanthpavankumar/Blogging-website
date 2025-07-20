<?php
session_start();
include('../config/db.php');


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user blogs
$stmt = $conn->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Blogs - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
    <a href="blogs/create_blog.php" class="btn btn-primary mb-3">+ Create New Blog</a>
    <a href="auth/logout.php" class="btn btn-outline-danger mb-3 float-end">Logout</a>

    <h4>Your Blogs:</h4>
    <?php if ($result->num_rows > 0): ?>
        <div class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="list-group-item">
                <h5><?= htmlspecialchars($row['title']) ?></h5>
                <small class="text-muted">Posted on <?= $row['created_at'] ?></small><br>
                <a href="blogs/blog.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
                <a href="blogs/edit_blog.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="blogs/delete_blog.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You haven't posted any blogs yet.</p>
    <?php endif; ?>

</body>
</html>

