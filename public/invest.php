<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'];

// Fetch plans
$plans = mysqli_query($conn,"SELECT * FROM investment_plans");

// Deposit / invest
if(isset($_POST['invest'])){
    $plan_id = $_POST['plan_id'];
    $amount = $_POST['amount'];

    // Fetch plan details
    $plan = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM investment_plans WHERE id='$plan_id'"));

    // Calculate profit
    $profit = $amount * ($plan['roi']/100);

    // End date
    $end_date = date('Y-m-d H:i:s', strtotime("+".$plan['duration_days']." days"));

    mysqli_query($conn,"INSERT INTO user_investments(user_id, plan_id, amount, end_date, profit) 
        VALUES ('$user_id', '$plan_id', '$amount', '$end_date', '$profit')");

    // Give referral bonus if user was referred
    $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT referred_by FROM users WHERE id='$user_id'"));
    if($user['referred_by']){
        $referrer = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id,total_bonus FROM users WHERE referral_code='".$user['referred_by']."'"));
        $bonus = $amount * 0.05; // 5% bonus
        mysqli_query($conn,"UPDATE users SET total_bonus = total_bonus + $bonus WHERE id=".$referrer['id']);
        mysqli_query($conn,"INSERT INTO referral_bonus(user_id,referred_user_id,amount) VALUES(".$referrer['id'].",$user_id,$bonus)");
    }

    echo "<script>alert('Investment successful! Profit: $".$profit."');</script>";
}
?>

<h2>Invest in a Plan</h2>
<form method="POST">
<select name="plan_id" required>
<?php while($plan = mysqli_fetch_assoc($plans)){ ?>
<option value="<?php echo $plan['id']; ?>"><?php echo $plan['name']." - ".$plan['roi']."% ROI"; ?></option>
<?php } ?>
</select>
<input type="number" name="amount" placeholder="Amount $" required>
<button name="invest">Invest</button>
</form>

