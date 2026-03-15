<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
    header("Location: login.php");
    exit();
}

$message = "";

if(isset($_POST['save'])){
    $wallet = $_POST['wallet'];

    if(isset($_SESSION['demo'])){
        $message = "Demo Mode: Wallet saved successfully.";
    } else {
        include "config.php";
        mysqli_query($conn,"UPDATE users SET wallet_address='$wallet' WHERE id='$user_id'");
        header("Location: dashboard.php");
        exit();
    }
}
?>


<?php if(!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
<form method="POST">
<h2>Add Wallet</h2>
<input type="text" name="wallet" required>
<button name="save">Save</button>
</form>