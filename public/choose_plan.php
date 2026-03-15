<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Define available plans with colors
$plans = [
    1 => ['name'=>'Basic Plan','min'=>300,'max'=>1999,'daily'=>2.5,'bonus'=>10,'withdraw_days'=>7,'color'=>'#3498db'],      // Blue
    2 => ['name'=>'Silver Plan','min'=>2000,'max'=>9999,'daily'=>3.0,'bonus'=>10,'withdraw_days'=>7,'color'=>'#7f8c8d'],   // Gray
    3 => ['name'=>'Platinum Plan','min'=>10000,'max'=>49999,'daily'=>4.0,'bonus'=>10,'withdraw_days'=>7,'color'=>'#f1c40f'], // Gold
    4 => ['name'=>'VIP Plan','min'=>50000,'max'=>10000000,'daily'=>5.0,'bonus'=>10,'withdraw_days'=>7,'color'=>'#8e44ad'], // Purple
];

if(isset($_GET['plan'])){
    $selected_plan = $_GET['plan'];
    if(isset($plans[$selected_plan])){
        $_SESSION['selected_plan_id'] = $selected_plan;
        header("Location: deposit.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Select Investment Plan</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 0 20px;
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 40px;
}
.plan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.plan-card {
    background: white;
    padding: 25px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    border-top: 6px solid;
}
.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.plan-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
}
.plan-card p {
    margin: 6px 0;
    color: #555;
}
.plan-card a {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #28a745;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
}
.plan-card a:hover {
    background: #218838;
}
</style>
</head>
<body>
<div class="container">
    <h2>Select an Investment Plan</h2>

<?php if(isset($_SESSION['demo'])): ?>
<div style="background:#fffae6; padding:10px; text-align:center; margin-bottom:20px; border:1px solid #ffd700;">
⚠️ Demo Mode: Selecting a plan will not create a real investment.
</div>
<?php endif; ?>

    <div class="plan-grid">
        <?php foreach($plans as $id=>$p){ ?>
        <div class="plan-card" style="border-top-color: <?php echo $p['color']; ?>;">
            <h3 style="color: <?php echo $p['color']; ?>;"><?php echo $p['name']; ?></h3>
            <p><strong>Amount:</strong> $<?php echo number_format($p['min'],2); ?> - $<?php echo number_format($p['max'],2); ?></p>
            <p><strong>Daily Profit:</strong> <?php echo $p['daily']; ?>%</p>
            <p><strong>Referral Bonus:</strong> <?php echo $p['bonus']; ?>%</p>
            <p><strong>Profit Withdrawal:</strong> <?php echo $p['withdraw_days']; ?> Day(s)</p>
            <?php if(isset($_SESSION['demo'])){ ?>
<a href="#" onclick="alert('Demo Mode: Deposits are disabled.'); return false;">Select & Invest ></a>
<?php } else { ?>
<a href="choose_plan.php?plan=<?php echo $id; ?>">Select & Invest ></a>
<?php } ?>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>