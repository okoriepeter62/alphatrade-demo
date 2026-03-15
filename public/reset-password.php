<?php
session_start();
include "config.php";

if (!isset($_SESSION['reset_user'])) {
    header("Location: login.php");
    exit();
}

$errors = [];

if (isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users SET password=? WHERE username=?"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $hashed,
            $_SESSION['reset_user']
        );
        mysqli_stmt_execute($stmt);

        unset($_SESSION['reset_user']);
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-box">
  <h2>Reset Password</h2>

  <?php foreach ($errors as $e) echo "<p class='error'>$e</p>"; ?>

  <form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button type="submit" name="reset">Change Password</button>
  </form>

</div>

</body>
</html>
