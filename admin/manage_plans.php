<?php
session_start();
include "../public/config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

// ADD PLAN
if(isset($_POST['add_plan'])){
    $name = $_POST['name'];
    $roi = $_POST['roi'];
    $duration = $_POST['duration'];
    mysqli_query($conn,"INSERT INTO investment_plans(name, roi, duration_days) VALUES('$name', '$roi', '$duration')");
}

// FETCH PLANS
$plans = mysqli_query($conn,"SELECT * FROM investment_plans");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Plans</title></head>
<body>
<h2>Investment Plans</h2>
<form method="POST">
<input type="text" name="name" placeholder="Plan Name" required>
<input type="number" name="roi" placeholder="ROI %" step="0.1" required>
<input type="number" name="duration" placeholder="Duration (days)" required>
<button name="add_plan">Add Plan</button>
</form>

<table border="1" cellpadding="10">
<tr><th>Name</th><th>ROI</th><th>Duration</th></tr>
<?php while($plan = mysqli_fetch_assoc($plans)){ ?>
<tr>
<td><?php echo $plan['name']; ?></td>
<td><?php echo $plan['roi']; ?>%</td>
<td><?php echo $plan['duration_days']; ?> days</td>
</tr>
<?php } ?>
</table>
</body>
</html>