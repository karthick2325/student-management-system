<?php
include("../../config/db.php");

// Validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch record
$result = mysqli_query($conn, "SELECT * FROM fees WHERE id = $id");
$data = mysqli_fetch_assoc($result);

// ❗ Invalid check
if (!$data) {
    die("Invalid Fee Record");
}

// ❗ Already paid check
if ($data['status'] === "Paid") {
    echo "<script>alert('Fees already fully paid'); window.location='view_fees.php';</script>";
    exit();
}

if (isset($_POST['pay'])) {

    $extra = floatval($_POST['amount']);

    if ($extra <= 0) {
        echo "<script>alert('Invalid amount');</script>";
    } else {

        $new_paid = $data['paid_amount'] + $extra;

        // Prevent overpayment
        if ($new_paid > $data['total_fee']) {
            $new_paid = $data['total_fee'];
        }

        $balance = $data['total_fee'] - $new_paid;
        $status = ($balance <= 0) ? "Paid" : "Pending";

        mysqli_query($conn, "
            UPDATE fees 
            SET paid_amount = '$new_paid',
                balance = '$balance',
                status = '$status'
            WHERE id = '$id'
        ");

        echo "<script>alert('Payment Updated'); window.location='view_fees.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Fees</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/fees/pay_fees.css">
</head>
<body>

<div class="container">

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<form method="POST">

<h2>Pay Fees</h2>

<!-- Info -->
<div class="info-box">
    <p><strong>Total Fee:</strong> ₹<?= number_format($data['total_fee'], 2) ?></p>
    <p><strong>Paid:</strong> ₹<?= number_format($data['paid_amount'], 2) ?></p>
    <p><strong>Balance:</strong> ₹<span id="balance"><?= number_format($data['balance'], 2) ?></span></p>
</div>

<!-- Input -->
<input 
    type="number" 
    name="amount" 
    id="amount"
    placeholder="Enter amount" 
    required
    min="1"
    max="<?= $data['balance'] ?>"
>

<!-- Warning -->
<p id="warning" style="color:red; display:none;">Amount exceeds balance!</p>

<button name="pay">Pay Now</button>

</form>

</div>

<!-- 🔥 LIVE JS -->
<script>
const amountInput = document.getElementById("amount");
const balanceText = document.getElementById("balance");
const warning = document.getElementById("warning");

const total = <?= $data['total_fee'] ?>;
const alreadyPaid = <?= $data['paid_amount'] ?>;

amountInput.addEventListener("input", function () {
    let extra = parseFloat(this.value) || 0;
    let newPaid = alreadyPaid + extra;

    if (newPaid > total) {
        warning.style.display = "block";
        newPaid = total;
    } else {
        warning.style.display = "none";
    }

    let newBalance = total - newPaid;

    balanceText.innerText = newBalance.toFixed(2);
});
</script>

</body>
</html>