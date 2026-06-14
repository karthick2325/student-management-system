<?php
include("../../config/db.php");

$students = mysqli_query($conn, "SELECT * FROM students");

if (isset($_POST['submit'])) {

    $student_id = intval($_POST['student_id']);
    $total_fee = floatval($_POST['total_fee']);
    $paid_amount = floatval($_POST['paid_amount']);

    // ❗ Validation
    if ($total_fee <= 0 || $paid_amount < 0) {
        echo "<script>alert('Invalid amount');</script>";
    } else {

        // ❗ Prevent overpayment
        if ($paid_amount > $total_fee) {
            $paid_amount = $total_fee;
        }

        // ❗ Check existing record
        $check = mysqli_query($conn, "SELECT * FROM fees WHERE student_id = $student_id");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Fees already added for this student');</script>";
        } else {

            $balance = $total_fee - $paid_amount;
            $status = ($balance <= 0) ? "Paid" : "Pending";

            mysqli_query($conn, "
                INSERT INTO fees (student_id, total_fee, paid_amount, balance, status)
                VALUES ('$student_id','$total_fee','$paid_amount','$balance','$status')
            ");

            echo "<script>alert('Fees Added Successfully'); window.location='view_fees.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Fees</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/fees/add_fees.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>Add Fees</h2>

<form method="POST">

<select name="student_id" required>
    <option value="">Select Student</option>
    <?php while($row = mysqli_fetch_assoc($students)) { ?>
        <option value="<?= $row['id'] ?>">
            <?= htmlspecialchars($row['name']) ?>
        </option>
    <?php } ?>
</select>

<input 
    type="number" 
    id="total_fee" 
    name="total_fee" 
    placeholder="Total Fee" 
    required
    min="1"
>

<input 
    type="number" 
    id="paid_amount" 
    name="paid_amount" 
    placeholder="Paid Amount" 
    required
    min="0"
>

<!-- 🔥 Live Balance -->
<p id="balance_text" style="font-weight:bold;"></p>

<!-- Warning -->
<p id="warning" style="color:red; display:none;">Paid amount exceeds total fee!</p>

<button name="submit">Save Fees</button>

</form>

<!-- 🔥 JS -->
<script>
document.getElementById("paid_amount").addEventListener("input", calculateBalance);
document.getElementById("total_fee").addEventListener("input", calculateBalance);

function calculateBalance() {
    let total = parseFloat(document.getElementById("total_fee").value) || 0;
    let paid = parseFloat(document.getElementById("paid_amount").value) || 0;

    let warning = document.getElementById("warning");

    if (paid > total) {
        warning.style.display = "block";
        paid = total;
    } else {
        warning.style.display = "none";
    }

    let balance = total - paid;

    document.getElementById("balance_text").innerHTML =
        "Balance: ₹ " + balance.toFixed(2);
}
</script>

</body>
</html>