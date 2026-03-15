<?php
include "config.php";

$today = date('Y-m-d H:i:s');
$investments = mysqli_query($conn,"SELECT * FROM user_investments WHERE status='Active' AND end_date <= '$today'");

while($inv = mysqli_fetch_assoc($investments)){
    // Add total return to user balance
    $total = $inv['amount'] + $inv['profit'];
    mysqli_query($conn,"UPDATE users SET balance = balance + $total WHERE id=".$inv['user_id']);
    
    // Mark investment completed
    mysqli_query($conn,"UPDATE user_investments SET status='Completed' WHERE id=".$inv['id']);
}
?>