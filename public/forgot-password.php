<?php
session_start();
include "config.php";

$errors = [];

if (isset($_POST['verify'])) {
    $username = trim($_POST['username']);

    if (empty($username)) {
        $errors[] = "Please enter your username.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $_SESSION['reset_user'] = $username;
            header("Location: reset-password.php");
            exit();
        } else {
            $errors[] = "User not found.";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-box">
  <h2>Forgot Password</h2>

  <?php foreach ($errors as $e) echo "<p class='error'>$e</p>"; ?>

  <form method="POST">
    <input type="text" name="username" placeholder="Enter your username" required>
    <button type="submit" name="verify">Verify</button>
  </form>

  <div class="links">
    <a href="login.php">Back to Login</a>
  </div>
</div>

</body>
</html>
