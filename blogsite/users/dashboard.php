<?php
session_start();
include('../config/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

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
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blogsite</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 text-white">Welcome, <b><?= htmlspecialchars($username) ?></b> 👋</span>
            <a href="../auth/logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Your Blogs</h2>
        <a href="../blogs/create_blog.php" class="btn btn-primary">+ Create New Blog</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-3">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text text-truncate" style="max-height:48px"><?= htmlspecialchars(mb_strimwidth($row['content'], 0, 100, '...')) ?></p>
                        <small class="text-muted mb-3">Posted on <?= htmlspecialchars($row['created_at']) ?></small>
                        <div class="mt-auto d-flex gap-2">
                            <a href="../blogs/blog.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="../blogs/edit_blog.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="../blogs/delete_blog.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-5">You haven't posted any blogs yet.</div>
    <?php endif; ?>
</div>

</body>
</html>
