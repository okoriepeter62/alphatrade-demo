<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Handle Card Application
if(isset($_POST['apply_card'])){
    $card_type = $_POST['card_type'];

    // Check KYC
    $kyc = mysqli_query($conn,"SELECT status FROM kyc WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
    if(mysqli_num_rows($kyc) == 0){
        $error = "Please submit KYC before applying for a card.";
    } else {
        $kycRow = mysqli_fetch_assoc($kyc);
        if($kycRow['status'] != "Approved"){
            $error = "Your KYC is pending approval.";
        } else {
            // Insert card request
            mysqli_query($conn,"INSERT INTO card_requests (user_id, card_type, status) 
                VALUES ('$user_id','$card_type','Pending')");
            $success = "Card application submitted! Admin will review it.";
        }
    }
}

// Fetch user card applications
$cards = mysqli_query($conn,"SELECT * FROM card_requests WHERE user_id='$user_id' ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Card Application</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #0b0f2b;
        }

        form {
            max-width: 500px;
            margin: 20px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        select, input[type="submit"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        input[type="submit"] {
            background: #0b0f2b;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        input[type="submit"]:hover {
            background: #f9b800;
            color: #0b0f2b;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
            border-radius: 6px;
            padding: 10px;
        }

        .message.error {
            background: #ffe2e2;
            color: #d9534f;
        }

        .message.success {
            background: #e0fbe0;
            color: #28a745;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        th {
            background: #0b0f2b;
            color: #fff;
        }

        td.status-Pending {
            color: #f0ad4e;
            font-weight: bold;
        }

        td.status-Approved {
            color: #28a745;
            font-weight: bold;
        }

        td.status-Rejected {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Card Application</h2>

<?php
if($error){
    echo "<div class='message error'>{$error}</div>";
}
if($success){
    echo "<div class='message success'>{$success}</div>";
}
?>

<form method="POST">
    <select name="card_type" required>
        <option value="">Select Card Type</option>
        <option value="Virtual">Virtual Card</option>
        <option value="Physical">Physical Card</option>
    </select>
    <input type="submit" name="apply_card" value="Apply for Card">
</form>

<?php if(mysqli_num_rows($cards) > 0){ ?>
<table>
    <tr>
        <th>Card Type</th>
        <th>Status</th>
        <th>Requested On</th>
    </tr>
    <?php while($c = mysqli_fetch_assoc($cards)){ ?>
    <tr>
        <td><?php echo $c['card_type']; ?></td>
        <td class="status-<?php echo $c['status']; ?>"><?php echo $c['status']; ?></td>
        <td><?php echo date("d M Y, H:i", strtotime($c['created_at'])); ?></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p style="text-align:center;">No card applications yet.</p>
<?php } ?>

</body>
</html>