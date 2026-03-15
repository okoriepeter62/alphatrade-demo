<?php
session_start();
include "../public/config.php";

$errors = [];

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
        $errors[] = "All fields are required";
    }

    if(empty($errors)){

        $stmt = mysqli_prepare($conn,"SELECT * FROM admin WHERE username=?");
        mysqli_stmt_bind_param($stmt,"s",$username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 1){

    $admin = mysqli_fetch_assoc($result);

    if(password_verify($password, $admin['password'])){

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header("Location: dashboard.php");
        exit();

    } else {
        $errors[] = "Invalid login details";
    }

} else {
    $errors[] = "Invalid login details";
}


    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>

body{
    font-family: Arial;
    background:#0b0f2b;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.login-box{
    background:white;
    padding:30px;
    width:350px;
    border-radius:10px;
}

h2{text-align:center;}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
}

button{
    width:100%;
    padding:12px;
    background:#0b0f2b;
    color:white;
    border:none;
    cursor:pointer;
}

.error{
    color:red;
    text-align:center;
}

</style>
</head>

<body>

<div class="login-box">

<h2>Admin Login</h2>

<?php foreach($errors as $error){ ?>
<p class="error"><?php echo $error; ?></p>
<?php } ?>

<form method="POST">

<input type="text" name="username" placeholder="Username">
<input type="password" name="password" placeholder="Password">

<button name="login">Login</button>

</form>

</div>

</body>
</html>