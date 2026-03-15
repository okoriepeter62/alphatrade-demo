<?php
session_start();
include "../public/config.php";

// SECURITY
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* ================= BLOCK / UNBLOCK ================= */
if(isset($_GET['block'])){
    $id = intval($_GET['block']);
    mysqli_query($conn,"UPDATE users SET status='Blocked' WHERE id='$id'");
    header("Location: manage_users.php");
    exit();
}

if(isset($_GET['unblock'])){
    $id = intval($_GET['unblock']);
    mysqli_query($conn,"UPDATE users SET status='Active' WHERE id='$id'");
    header("Location: manage_users.php");
    exit();
}

/* DELETE USER SAFELY */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']); // ensure integer

    // Delete related transactions
    mysqli_query($conn, "DELETE FROM transactions WHERE user_id='$id'");

    // Delete KYC records
    mysqli_query($conn, "DELETE FROM kyc WHERE user_id='$id'");

    // Only delete wallet if table exists
    $check_wallet = mysqli_query($conn, "SHOW TABLES LIKE 'wallet'");
    if(mysqli_num_rows($check_wallet) > 0){
        mysqli_query($conn, "DELETE FROM wallet WHERE user_id='$id'");
    }

    // Finally delete the user
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");

    header("Location: manage_users.php");
    exit();
}

/* ================= FETCH USERS ================= */
$users = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0;padding:0;}
.container{width:90%;margin:auto;padding:20px;}
h2{margin-top:0;}
table{width:100%;border-collapse:collapse;background:white;margin-top:20px;}
th,td{padding:10px;border-bottom:1px solid #ddd;text-align:center;}
th{background:#eee;}
.btn{padding:6px 12px;color:white;text-decoration:none;border-radius:4px;margin:2px;}
.green{background:green;}
.red{background:red;}
.gray{background:gray;}
</style>
</head>
<body>
<div class="container">
<h2>Manage Users</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<table>
<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Balance</th>
<th>Total Deposit</th>
<th>Total Withdrawal</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($user = mysqli_fetch_assoc($users)){ ?>
<tr>
<td><?php echo $user['id']; ?></td>
<td><?php echo htmlspecialchars($user['username']); ?></td>
<td><?php echo htmlspecialchars($user['email']); ?></td>
<td>$<?php echo number_format($user['balance'],2); ?></td>
<td>$<?php echo number_format($user['total_deposit'],2); ?></td>
<td>$<?php echo number_format($user['total_withdraw'],2); ?></td>
<td><?php echo $user['status'] ?? "Active"; ?></td>
<td>
    <!-- Block / Unblock -->
    <?php if(($user['status'] ?? "Active") == "Blocked"){ ?>
        <a class="btn green" href="?unblock=<?php echo $user['id']; ?>">Unblock</a>
    <?php } else { ?>
        <a class="btn red" href="?block=<?php echo $user['id']; ?>">Block</a>
    <?php } ?>

    <!-- Delete -->
    <a class="btn gray" href="?delete=<?php echo $user['id']; ?>" 
       onclick="return confirm('Are you sure you want to delete this user and all related data?');">
       Delete
    </a>
</td>
</tr>
<?php } ?>
</table>
</div>
</body>
</html>