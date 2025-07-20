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

// Fetch the blog post to edit
if ($blog_id) {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    if (!$blog) {
        die("You do not have permission to edit this blog.");
    }
} else {
    die("Blog ID missing.");
}

// Update the blog post
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);

    if (empty($title) || empty($content)) {
        $msg = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ?, image_url = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $title, $content, $image_url, $blog_id, $user_id);
        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit;
        } else {
            $msg = "Update failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
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
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="mb-4 text-primary text-center">Edit Blog Post</h2>
                    <?php if ($msg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                   value="<?= htmlspecialchars($blog['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" rows="8" class="form-control" required><?= htmlspecialchars($blog['content']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL (optional)</label>
                            <input type="url" id="image_url" name="image_url" class="form-control"
                                   value="<?= htmlspecialchars($blog['image_url']) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Blog</button>
                    </form>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="../index.php" class="btn btn-link">&larr; Back to Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
