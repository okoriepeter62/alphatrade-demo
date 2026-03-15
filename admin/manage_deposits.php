<?php
session_start();
include "../public/config.php";

/* SECURITY CHECK */
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* APPROVE DEPOSIT */
if(isset($_GET['approve'])){

    $id = $_GET['approve'];

    // Get transaction
    $query = mysqli_query($conn,"SELECT * FROM transactions WHERE id='$id'");
    $trx = mysqli_fetch_assoc($query);

    if($trx['status'] == 'Pending'){

        $user_id = $trx['user_id'];
        $amount = $trx['amount'];

        // Update transaction status
        mysqli_query($conn,"UPDATE transactions SET status='Approved' WHERE id='$id'");

        // Add balance to user
        mysqli_query($conn,"UPDATE users SET balance = balance + $amount, total_deposit = total_deposit + $amount WHERE id='$user_id'");
    }
}

/* REJECT DEPOSIT */
if(isset($_GET['reject'])){

    $id = $_GET['reject'];

    mysqli_query($conn,"UPDATE transactions SET status='Rejected' WHERE id='$id'");
}

/* FETCH DEPOSITS */
$deposits = mysqli_query($conn,"
    SELECT transactions.*, users.username 
    FROM transactions 
    JOIN users ON users.id = transactions.user_id
    WHERE type='Deposit'
    ORDER BY transactions.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Deposits</title>

<style>
body{font-family:Arial;background:#f4f6f9;margin:0;}
.topbar{background:#0b0f2b;color:white;padding:15px;}
.container{padding:20px;}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

.btn{
    padding:6px 12px;
    text-decoration:none;
    color:white;
    border-radius:4px;
}

.green{background:#5cb85c;}
.red{background:#d9534f;}
.gray{background:#777;}
</style>

</head>

<body>

<div class="topbar">
<h2>Manage Deposits</h2>
<a href="dashboard.php" style="color:white;">← Back to Dashboard</a>
</div>

<div class="container">

<table>

<tr>
<th>User</th>
<th>Amount</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($deposits)){ ?>

<tr>
<td><?php echo $row['username']; ?></td>
<td>$<?php echo number_format($row['amount'],2); ?></td>
<td><?php echo $row['status']; ?></td>

<td>

<?php if($row['status'] == "Pending"){ ?>

<a class="btn green" href="?approve=<?php echo $row['id']; ?>">Approve</a>
<a class="btn red" href="?reject=<?php echo $row['id']; ?>">Reject</a>

<?php } else { ?>

<span class="btn gray">Completed</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>