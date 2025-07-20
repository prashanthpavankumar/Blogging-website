<?php
session_start();
include '../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = "Username or email already taken.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['username'] = $username;
            header("Location: ../index.php");
            exit;
        } else {
            $msg = "Registration failed. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="mb-3 text-primary text-center">Sign Up</h2>
                    <?php if ($msg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" required placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" required placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" required placeholder="Password">
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Register</button>
                    </form>
                    <div class="mt-3 text-center">
                        Already have an account? <a href="login.php">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
