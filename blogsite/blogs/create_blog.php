<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $image_url = trim($_POST["image_url"]); // optional
    $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($content)) {
        $msg = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, content, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $content, $image_url);
        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit;
        } else {
            $msg = "Failed to post blog.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blogsite</a>
        <div class="d-flex">
            <a class="btn btn-outline-light" href="../index.php">&larr; Home</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="mb-4 text-primary text-center">Create a New Blog Post</h2>
                    <?php if ($msg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" required placeholder="Blog Title">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" rows="8" class="form-control" required placeholder="Write your blog here..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL (optional)</label>
                            <input type="url" id="image_url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Post Blog</button>
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
