<?php
session_start();
include "../public/config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

//////////////////////////////////////////////////////
// FETCH PENDING DEPOSITS
//////////////////////////////////////////////////////
$deposits = mysqli_query($conn, "
    SELECT t.*, u.username 
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    WHERE t.type='Deposit' AND t.status='Pending'
    ORDER BY t.id DESC
");

//////////////////////////////////////////////////////
// FETCH PENDING WITHDRAWALS
//////////////////////////////////////////////////////
$withdrawals = mysqli_query($conn, "
    SELECT t.*, u.username 
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    WHERE t.type='Withdraw' AND t.status='Pending'
    ORDER BY t.id DESC
");

//////////////////////////////////////////////////////
// APPROVE DEPOSIT
//////////////////////////////////////////////////////
if(isset($_GET['approve'])){
    $deposit_id = intval($_GET['approve']);
    $txn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transactions WHERE id='$deposit_id'"));

    if($txn){
        $user_id = $txn['user_id'];
        $duration = intval($txn['duration_days']);
        if($duration <= 0) $duration = 7;

        $start_date = date("Y-m-d H:i:s");
        $end_date = date("Y-m-d H:i:s", strtotime("+$duration days"));

        mysqli_query($conn,"
            UPDATE transactions SET
                status='Approved',
                start_date='$start_date',
                end_date='$end_date',
                last_profit_update='$start_date'
            WHERE id='$deposit_id'
        ");

        // REFERRAL BONUS (first deposit)
        $checkDeposit = mysqli_query($conn,"
            SELECT id FROM transactions
            WHERE user_id='$user_id' AND type='Deposit' AND status='Approved'
        ");
        if(mysqli_num_rows($checkDeposit) == 1){
            $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT referred_by FROM users WHERE id='$user_id'"));
            if(!empty($user['referred_by'])){
                $referrer = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE referral_code='".$user['referred_by']."'"));
                if($referrer){
                    $bonus = $txn['amount'] * 0.05; // 5%
                    mysqli_query($conn,"
                        UPDATE users SET
                            balance = balance + $bonus,
                            total_bonus = total_bonus + $bonus
                        WHERE id='".$referrer['id']."'
                    ");
                }
            }
        }
    }
    header("Location: deposit_approval.php");
    exit();
}

//////////////////////////////////////////////////////
// REJECT DEPOSIT
//////////////////////////////////////////////////////
if(isset($_GET['reject'])){
    $deposit_id = intval($_GET['reject']);
    mysqli_query($conn,"UPDATE transactions SET status='Rejected' WHERE id='$deposit_id'");
    header("Location: deposit_approval.php");
    exit();
}

//////////////////////////////////////////////////////
// APPROVE WITHDRAWAL
//////////////////////////////////////////////////////
if(isset($_GET['approve_withdraw'])){
    $withdraw_id = intval($_GET['approve_withdraw']);
    $txn = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM transactions WHERE id='$withdraw_id'"));
    if($txn){
        // Just mark it approved, funds already reserved in user's balance
        mysqli_query($conn,"UPDATE transactions SET status='Approved' WHERE id='$withdraw_id'");
    }
    header("Location: deposit_approval.php");
    exit();
}

//////////////////////////////////////////////////////
// REJECT WITHDRAWAL
//////////////////////////////////////////////////////
if(isset($_GET['reject_withdraw'])){
    $withdraw_id = intval($_GET['reject_withdraw']);
    $txn = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM transactions WHERE id='$withdraw_id'"));
    if($txn){
        // Return money to user balance
        mysqli_query($conn,"UPDATE users SET balance = balance + {$txn['amount']} WHERE id={$txn['user_id']}");
        // Mark withdrawal as rejected
        mysqli_query($conn,"UPDATE transactions SET status='Rejected' WHERE id='$withdraw_id'");
    }
    header("Location: deposit_approval.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - Deposit & Withdrawal Approval</title>
<style>
body { font-family: Arial; background:#f4f6f9; padding:20px; }
h2 { margin-bottom:15px; }
a { text-decoration:none; }
table { width:100%; background:white; border-collapse:collapse; font-size:13px; margin-bottom:30px;}
th, td { padding:8px; border:1px solid #ddd; text-align:center; }
th { background:#007bff; color:white; }
.btn { padding:5px 8px; color:white; border-radius:4px; font-size:12px; margin:3px; display:inline-block; }
.green{background:green;}
.red{background:red;}
.gray{background:gray;}
</style>
</head>
<body>

<h2>Pending Deposits</h2>
<a href="dashboard.php">← Back to Dashboard</a>
<table>
<tr>
<th>User</th>
<th>Amount</th>
<th>Method</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php if(mysqli_num_rows($deposits) > 0): ?>
    <?php while($d = mysqli_fetch_assoc($deposits)): ?>
        <tr>
            <td><?php echo htmlspecialchars($d['username']); ?></td>
            <td>$<?php echo number_format($d['amount'],2); ?></td>
            <td><?php echo htmlspecialchars($d['method']); ?></td>
            <td><?php echo htmlspecialchars($d['status']); ?></td>
            <td>
                <a class="btn green" href="?approve=<?php echo $d['id']; ?>">Approve</a>
                <a class="btn red" href="?reject=<?php echo $d['id']; ?>">Reject</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5" style="text-align:center;">No pending deposits</td></tr>
<?php endif; ?>
</table>

<h2>Pending Withdrawals</h2>
<table>
<tr>
<th>User</th>
<th>Amount</th>
<th>Method</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php if(mysqli_num_rows($withdrawals) > 0): ?>
    <?php while($w = mysqli_fetch_assoc($withdrawals)): ?>
        <tr>
            <td><?php echo htmlspecialchars($w['username']); ?></td>
            <td>$<?php echo number_format($w['amount'],2); ?></td>
            <td><?php echo htmlspecialchars($w['method']); ?></td>
            <td><?php echo htmlspecialchars($w['status']); ?></td>
            <td>
                <a class="btn green" href="?approve_withdraw=<?php echo $w['id']; ?>">Approve</a>
                <a class="btn red" href="?reject_withdraw=<?php echo $w['id']; ?>">Reject</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5" style="text-align:center;">No pending withdrawals</td></tr>
<?php endif; ?>
</table>

</body>
</html>