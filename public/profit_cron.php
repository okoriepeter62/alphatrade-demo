<?php
include "config.php";

$plans = mysqli_query($conn,"
SELECT * FROM transactions
WHERE status='Approved'
AND start_date IS NOT NULL
AND end_date IS NOT NULL
AND last_profit_update < NOW()
");

while($plan = mysqli_fetch_assoc($plans)){

    $last_update = strtotime($plan['last_profit_update']);
    $now = time();
    $end = strtotime($plan['end_date']);

    $seconds_passed = max(0, min($now, $end) - $last_update);
    if($seconds_passed <= 0) continue;

    $profit_per_sec = ($plan['amount'] * ($plan['daily_percent']/100)) / 86400;
    $profit = $profit_per_sec * $seconds_passed;

    // Update user balance
    mysqli_query($conn,"UPDATE users SET balance = balance + $profit WHERE id=".$plan['user_id']);

    // Update last_profit_update
    mysqli_query($conn,"UPDATE transactions SET last_profit_update = NOW() WHERE id=".$plan['id']);
}
?>