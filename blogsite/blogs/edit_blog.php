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
</head>
<body>
<h2>Edit Blog Post</h2>
<?php if ($msg) echo "<p style='color:red;'>$msg</p>"; ?>

<form method="post" action="">
    <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required><br><br>
    <textarea name="content" rows="10" cols="50" required><?= htmlspecialchars($blog['content']) ?></textarea><br><br>
    <input type="text" name="image_url" value="<?= htmlspecialchars($blog['image_url']) ?>" placeholder="Image URL (optional)"><br><br>
    <button type="submit">Update Blog</button>
</form>

<p><a href="../index.php">← Back to Home</a></p>
</body>
</html>

