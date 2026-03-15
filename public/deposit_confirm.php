<?php
session_start();
include "config.php";

$method = $_GET['method'];
$user_id = $_SESSION['user_id'];

if($method == "Bank"){
    echo "<h2>Bank Deposit Instructions</h2>";
    echo "<p>Please transfer the amount to the following bank account:</p>";
    echo "<ul>
            <li>Bank: GTBank</li>
            <li>Account Name: InvestPro Ltd</li>
            <li>Account Number: 0123456789</li>
          </ul>";
    echo "<p>After transfer, your deposit will be verified by admin.</p>";
} elseif($method == "Crypto") {
    // Generate unique wallet address for this user (if needed)
    $user_wallet = mysqli_fetch_assoc(mysqli_query($conn,"SELECT wallet_address FROM users WHERE id='$user_id'"))['wallet_address'];
    if(!$user_wallet){
        // You can generate a new address via exchange API or use a fixed address
        $user_wallet = "0xABCDEF1234567890"; // Example ETH address
        mysqli_query($conn,"UPDATE users SET wallet_address='$user_wallet' WHERE id='$user_id'");
    }
    echo "<h2>Crypto Deposit Instructions</h2>";
    echo "<p>Send the exact amount to your crypto wallet address:</p>";
    echo "<p><strong>$user_wallet</strong></p>";
    echo "<p>After payment, admin will confirm the deposit.</p>";
}
?>
<a href="dashboard.php"><button>Back to Dashboard</button></a>