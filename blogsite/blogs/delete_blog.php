<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$blog_id = $_GET['id'] ?? null;
$msg = "";

// Fetch blog (to confirm ownership & show title)
if ($blog_id) {
    $stmt = $conn->prepare("SELECT id, title FROM blogs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
} else {
    $blog = null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $blog) {
    // Delete after confirmation
    $del_stmt = $conn->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $blog_id, $user_id);
    $del_stmt->execute();
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blogsite</a>
    </div>
</nav>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <?php if ($blog): ?>
                        <h3 class="text-danger text-center mb-4">Delete Blog</h3>
                        <p class="lead text-center">Are you sure you want to <b>permanently delete</b> the blog post:</p>
                        <p class="fw-bold text-center text-primary">"<?= htmlspecialchars($blog['title']) ?>"</p>
                        <form method="post">
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <button type="submit" class="btn btn-danger px-4">Yes, Delete</button>
                                <a href="../index.php" class="btn btn-secondary px-4">Cancel</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            Blog not found or you do not have permission to delete it.
                        </div>
                        <div class="text-center">
                            <a href="../index.php" class="btn btn-primary">Back to Home</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
