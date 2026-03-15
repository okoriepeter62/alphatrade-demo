<?php
session_start();
include "../public/config.php";

/* SECURITY CHECK */
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* COUNT DATA */

// Total Users
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users"))['total'];

// Total Deposits
$totalDeposits = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(amount) AS total FROM transactions WHERE type='Deposit' AND status='Approved'"))['total'] ?? 0;

// Total Withdrawals
$totalWithdrawals = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(amount) AS total FROM transactions WHERE type='Withdraw' AND status='Approved'"))['total'] ?? 0;

// Pending Deposits
$pendingDeposits = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM transactions WHERE type='Deposit' AND status='Pending'"))['total'];

// Pending Withdrawals
$pendingWithdrawals = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM transactions WHERE type='Withdraw' AND status='Pending'"))['total'];

?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    margin:0;
}

/* TOP BAR */
.topbar{
    background:#0b0f2b;
    color:white;
    padding:15px;
    display:flex;
    justify-content:space-between;
}

/* GRID */
.container{
    padding:20px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

/* CARDS */
.card{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}

.card h2{
    margin:0;
    color:#0b0f2b;
}

/* BUTTONS */
.btn{
    display:block;
    padding:12px;
    margin:10px 0;
    background:#0b0f2b;
    color:white;
    text-align:center;
    text-decoration:none;
    border-radius:6px;
}

.btn.red{background:#d9534f;}
.btn.green{background:#5cb85c;}

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h2>Admin Dashboard</h2>
    <div>
        Welcome, <?php echo $_SESSION['admin_username']; ?> |
        <a href="logout.php" style="color:white;">Logout</a>
    </div>
</div>

<div class="container">

<!-- STATS -->
<div class="grid">

<div class="card">
<h3>Total Users</h3>
<h2><?php echo $totalUsers; ?></h2>
</div>

<div class="card">
<h3>Total Deposits</h3>
<h2>$<?php echo number_format($totalDeposits,2); ?></h2>
</div>

<div class="card">
<h3>Total Withdrawals</h3>
<h2>$<?php echo number_format($totalWithdrawals,2); ?></h2>
</div>

<div class="card">
<h3>Pending Deposits</h3>
<h2><?php echo $pendingDeposits; ?></h2>
</div>

<div class="card">
<h3>Pending Withdrawals</h3>
<h2><?php echo $pendingWithdrawals; ?></h2>
</div>


<div class="card">
  <h4>Pending Deposits</h4>
  <p>Check and approve deposits.</p>
  <a href="deposit_approval.php"><button>Go ></button></a>
</div>


</div>

<!-- QUICK ACTIONS -->
<div class="card" style="margin-top:20px;">

<h3>Quick Actions</h3>

<a class="btn green" href="manage_deposits.php">Approve Deposits</a>
<a class="btn red" href="manage_withdrawals.php">Approve Withdrawals</a>
<a class="btn" href="manage_users.php">Manage Users</a>
<a class="btn" href="kyc_approval.php">Approve KYC</a>
<a class="btn green" href="manage_transfers.php">View Transfers</a>
<a class="btn" href="manage_cards.php">Manage Card Requests</a>

</div>

</div>

</body>
</html>