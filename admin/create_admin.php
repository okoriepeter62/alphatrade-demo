<?php
include "../public/config.php"; // your DB connection

$username = "admin";
$password = password_hash("Admin@123", PASSWORD_DEFAULT);

mysqli_query($conn, "INSERT INTO admin (username, password) VALUES ('$username', '$password')");
echo "Admin created!";
?>