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
</head>
<body>

<h2>Create a New Blog Post</h2>
<?php if ($msg) echo "<p style='color:red;'>$msg</p>"; ?>

<form method="post" action="">
    <input type="text" name="title" placeholder="Title" required><br><br>
    <textarea name="content" rows="10" cols="50" placeholder="Write your blog here..." required></textarea><br><br>
    <input type="text" name="image_url" placeholder="Image URL (optional)"><br><br>
    <button type="submit">Post Blog</button>
</form>

<p><a href="../index.php">← Back to Home</a></p>

</body>
</html>

