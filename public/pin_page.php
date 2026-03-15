<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'];

// Handle PIN creation / change
if(isset($_POST['set_pin'])){
    $pin = $_POST['pin'];
    $pin_confirm = $_POST['pin_confirm'];

    if($pin !== $pin_confirm){
        echo "PINs do not match";
        exit();
    }

    if(strlen($pin) < 4){
        echo "PIN must be at least 4 digits";
        exit();
    }

    // Hash PIN
    $hashed_pin = password_hash($pin, PASSWORD_DEFAULT);

    // Save to database
    mysqli_query($conn,"UPDATE users SET withdraw_pin='$hashed_pin' WHERE id='$user_id'");
    echo "Withdrawal PIN set successfully!";
}
?>

<form method="POST">
<input type="password" name="pin" placeholder="Enter New PIN" required>
<input type="password" name="pin_confirm" placeholder="Confirm PIN" required>
<button name="set_pin">Set/Change PIN</button>
</form>