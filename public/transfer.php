<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
    header("Location: login.php");
    exit();
}

// Initialize message variables
$error = "";
$success = "";

if(isset($_POST['transfer'])){
    $amount = floatval($_POST['amount']);
    $receiver_username = mysqli_real_escape_string($conn,$_POST['receiver']);
    $entered_pin = $_POST['transaction_pin'];

    // Fetch sender info
    $sender = mysqli_fetch_assoc(mysqli_query($conn,"SELECT balance, withdraw_pin FROM users WHERE id='$user_id'"));

    // Check if PIN is set
    if(!$sender['withdraw_pin']){
        $error = "You must set a 4-digit PIN before making transfers.";
    }
    // Check PIN
    elseif(!password_verify($entered_pin, $sender['withdraw_pin'])){
        $error = "Incorrect PIN.";
    }
    // Check min/max transfer
    elseif($amount < 10 || $amount > 200){
        $error = "Transfer amount must be between $10 and $200.";
    }
    // Check balance
    elseif($amount > $sender['balance']){
        $error = "Insufficient balance.";
    } else {
        // Fetch receiver
        $receiver = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE username='$receiver_username'"));
        if(!$receiver){
            $error = "Receiver not found.";
        } else {
            $receiver_id = $receiver['id'];

            // Deduct sender
            mysqli_query($conn,"UPDATE users SET balance = balance - $amount WHERE id='$user_id'");
            // Credit receiver
            mysqli_query($conn,"UPDATE users SET balance = balance + $amount WHERE id='$receiver_id'");
            // Record transfer
            mysqli_query($conn,"INSERT INTO transfers (sender_id,receiver_id,amount,status) VALUES ('$user_id','$receiver_id','$amount','Completed')");
            $success = "Transfer successful!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Transfer</title>
    <style>
        /* =======================
           General Page Styling
        ======================= */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* =======================
           Form Container
        ======================= */
        form {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Form Heading */
        form h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0b0f2b;
        }

        /* Input Fields */
        input[type="text"],
        input[type="number"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus {
            border-color: #0b0f2b;
            outline: none;
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 12px;
            background: #0b0f2b;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }

        button:hover {
            background: #f9b800;
            color: #0b0f2b;
        }

        /* Messages */
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
            border-radius: 6px;
            padding: 10px;
        }

        .message.error {
            background: #ffe2e2;
            color: #d9534f;
        }

        .message.success {
            background: #e0fbe0;
            color: #28a745;
        }

        /* Responsive */
        @media (max-width: 600px) {
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>Send Transfer</h2>

    <?php
    if($error){
        echo "<div class='message error'>{$error}</div>";
    }
    if($success){
        echo "<div class='message success'>{$success}</div>";
    }
    ?>

    <input type="text" name="receiver" placeholder="Receiver Username" required>
    <input type="number" name="amount" placeholder="Amount ($10-$200)" required>
    <input type="password" name="transaction_pin" placeholder="Enter 4-digit PIN" required maxlength="4">
    <button name="transfer">Send</button>
</form>

</body>
</html>