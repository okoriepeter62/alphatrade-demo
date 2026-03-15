<?php
session_start();
include "../public/config.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* APPROVE */
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    mysqli_query($conn,"UPDATE kyc SET status='Approved' WHERE id='$id'");
}

/* REJECT */
if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);
    mysqli_query($conn,"UPDATE kyc SET status='Rejected' WHERE id='$id'");
}

/* FETCH KYC */
$kycs = mysqli_query($conn,"
SELECT kyc.*, users.username 
FROM kyc 
JOIN users ON users.id = kyc.user_id
ORDER BY kyc.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>KYC Approval Panel</title>
<style>
body{
    font-family:Arial;
    background:#f4f6f9;
    padding:20px;
}
h2{margin-bottom:15px;}
a{text-decoration:none;color:#007bff;}
table{
    width:100%;
    background:white;
    border-collapse:collapse;
    font-size:13px;
}
th, td{
    padding:8px;
    border:1px solid #ddd;
    text-align:center;
}
th{background:#007bff;color:white;}
.btn{
    padding:5px 8px;
    color:white;
    border-radius:4px;
    font-size:12px;
    margin:3px; /* gap between buttons */
    display:inline-block;
}
.green{background:green;}
.red{background:red;}
.gray{background:gray;}
img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:4px;
}
</style>
</head>
<body>

<h2>KYC Approval Panel</h2>
<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<table>
<tr>
<th>User</th>
<th>Full Name</th>
<th>DOB</th>
<th>Gender</th>
<th>Nationality</th>
<th>Email</th>
<th>Phone</th>
<th>Address</th>
<th>ID Type</th>
<th>ID Number</th>
<th>Front ID</th>
<th>Back ID</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($kycs)){ ?>
<tr>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['fullname']; ?></td>
<td><?php echo $row['dob']; ?></td>
<td><?php echo $row['gender']; ?></td>
<td><?php echo $row['nationality']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['phone']; ?></td>
<td><?php echo $row['address']; ?></td>
<td><?php echo $row['id_type']; ?></td>
<td><?php echo $row['id_number']; ?></td>

<td>
<?php if(!empty($row['id_front']) && file_exists("../public/uploads/".$row['id_front'])): ?>
<a href="../public/uploads/<?php echo $row['id_front']; ?>" target="_blank">
    <img src="../public/uploads/<?php echo $row['id_front']; ?>" alt="Front ID">
</a>
<?php else: ?>
N/A
<?php endif; ?>
</td>

<td>
<?php if(!empty($row['id_back']) && file_exists("../public/uploads/".$row['id_back'])): ?>
<a href="../public/uploads/<?php echo $row['id_back']; ?>" target="_blank">
    <img src="../public/uploads/<?php echo $row['id_back']; ?>" alt="Back ID">
</a>
<?php else: ?>
N/A
<?php endif; ?>
</td>

<td>
<?php
if($row['status']=="Pending"){
    echo "<span style='color:orange;'>Pending</span>";
}
elseif($row['status']=="Approved"){
    echo "<span style='color:green;'>Approved</span>";
}
else{
    echo "<span style='color:red;'>Rejected</span>";
}
?>
</td>

<td>
<?php if($row['status']=="Pending"){ ?>
<a class="btn green" href="?approve=<?php echo $row['id']; ?>">Approve</a>
<a class="btn red" href="?reject=<?php echo $row['id']; ?>">Reject</a>
<?php } else { ?>
<span class="btn gray">Done</span>
<?php } ?>
</td>

</tr>
<?php } ?>
</table>

</body>
</html>