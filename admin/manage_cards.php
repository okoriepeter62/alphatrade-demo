<?php
session_start();
include "../public/config.php";

// SECURITY CHECK
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

// Approve card
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    mysqli_query($conn,"UPDATE card_requests SET status='Approved' WHERE id='$id'");
}

// Reject card
if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);
    mysqli_query($conn,"UPDATE card_requests SET status='Rejected' WHERE id='$id'");
}

// Fetch all card requests
$cards = mysqli_query($conn,"SELECT card_requests.*, users.username 
    FROM card_requests 
    JOIN users ON users.id = card_requests.user_id
    ORDER BY card_requests.id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Card Requests</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        h2{
            text-align: center;
            color: #0b0f2b;
        }

        table{
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        th, td{
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        th{
            background: #0b0f2b;
            color: #fff;
        }

        td.status-Pending{
            color: #f0ad4e;
            font-weight: bold;
        }

        td.status-Approved{
            color: #28a745;
            font-weight: bold;
        }

        td.status-Rejected{
            color: #d9534f;
            font-weight: bold;
        }

        a.btn{
            padding: 6px 12px;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            margin: 0 2px;
            display: inline-block;
            font-size: 14px;
        }

        a.btn.green{ background: #5cb85c; }
        a.btn.red{ background: #d9534f; }
        a.btn.gray{ background: #777; pointer-events: none; }
    </style>
</head>
<body>

<h2>Manage Card Requests</h2>

<table>
    <tr>
        <th>User</th>
        <th>Card Type</th>
        <th>Status</th>
        <th>Requested On</th>
        <th>Action</th>
    </tr>
    <?php while($c = mysqli_fetch_assoc($cards)){ ?>
    <tr>
        <td><?php echo $c['username']; ?></td>
        <td><?php echo $c['card_type']; ?></td>
        <td class="status-<?php echo $c['status']; ?>"><?php echo $c['status']; ?></td>
        <td><?php echo date("d M Y, H:i", strtotime($c['created_at'])); ?></td>
        <td>
            <?php if($c['status'] == 'Pending'){ ?>
                <a class="btn green" href="?approve=<?php echo $c['id']; ?>">Approve</a>
                <a class="btn red" href="?reject=<?php echo $c['id']; ?>">Reject</a>
            <?php } else { ?>
                <span class="btn gray">Completed</span>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>