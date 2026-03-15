<?php

function updateUserProfit($conn, $user_id){

    $user_id = intval($user_id);

    $plans = mysqli_query($conn,"
        SELECT * FROM transactions
        WHERE user_id='$user_id'
        AND status='Approved'
        AND end_date IS NOT NULL
        AND start_date IS NOT NULL
        AND end_date IS NOT NULL
        AND end_date > NOW()
    ");

    while($plan = mysqli_fetch_assoc($plans)){

        if(empty($plan['last_profit_update'])) {
            continue; // skip if no last update
        }

        $last_update = strtotime($plan['last_profit_update']);
        $now = time();

        $end = strtotime($plan['end_date']);
$seconds_passed = max(0, min($now, $end) - $last_update);

        if($seconds_passed > 0){

            $profit_per_second = 
                ($plan['amount'] * ($plan['daily_percent'] / 100)) / 86400;

            $profit = $profit_per_second * $seconds_passed;

            // Add profit to user balance
            mysqli_query($conn,"
                UPDATE users 
                SET balance = balance + $profit
                WHERE id='".$plan['user_id']."'
            ");

            // Update last profit update time
            mysqli_query($conn,"
                UPDATE transactions 
                SET last_profit_update = NOW()
                WHERE id='".$plan['id']."'
            ");
        }
    }
}

?>