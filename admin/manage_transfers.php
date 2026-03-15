<?php
session_start();
include "../public/config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

// Fetch all transfers
$transfers = mysqli_query($conn,"
    SELECT t.*, u1.username AS sender_name, u2.username AS receiver_name
    FROM transfers t
    JOIN users u1 ON t.sender_id = u1.id
    JOIN users u2 ON t.receiver_id = u2.id
    ORDER BY t.id DESC
");
?>

<h2>All Transfers</h2>
<table border="1" cellpadding="10">
<tr>
<th>Sender</th>
<th>Receiver</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>
<?php while($row=mysqli_fetch_assoc($transfers)): ?>
<tr>
<td><?php echo $row['sender_name']; ?></td>
<td><?php echo $row['receiver_name']; ?></td>
<td>$<?php echo number_format($row['amount'],2); ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['created_at']; ?></td>
</tr>
<?php endwhile; ?>
</table>