<?php
include("../../config/db.php");

// 🔥 FILTER VALUES
$student_filter = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$status_filter  = isset($_GET['status']) ? $_GET['status'] : "";

// 🔥 FETCH STUDENTS FOR DROPDOWN
$students_list = mysqli_query($conn, "SELECT id, name FROM students");

// 🔥 BASE QUERY
$query = "
SELECT fees.*, students.name 
FROM fees
LEFT JOIN students ON fees.student_id = students.id
WHERE 1
";

// 🔥 APPLY FILTERS
if ($student_filter > 0) {
    $query .= " AND fees.student_id = '$student_filter'";
}

if (!empty($status_filter)) {
    $status_filter = mysqli_real_escape_string($conn, $status_filter);
    $query .= " AND fees.status = '$status_filter'";
}

$query .= " ORDER BY fees.id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fees Report</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/fees/view_fees.css">
</head>
<body>

<div class="container">

<!-- ✅ BACK BUTTON FIX -->
<div class="top-bar">
    <a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<h2>Fees Report</h2>

<!-- 🔥 FILTER FORM -->
<form method="GET" class="filter-form">

<select name="student_id">
    <option value="">All Students</option>
    <?php while($s = mysqli_fetch_assoc($students_list)) { ?>
        <option value="<?= $s['id'] ?>" <?= ($student_filter == $s['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['name']) ?>
        </option>
    <?php } ?>
</select>

<select name="status">
    <option value="">All Status</option>
    <option value="Paid" <?= ($status_filter == "Paid") ? 'selected' : '' ?>>Paid</option>
    <option value="Pending" <?= ($status_filter == "Pending") ? 'selected' : '' ?>>Pending</option>
</select>

<button type="submit">Filter</button>
<a href="view_fees.php" class="reset-btn">Reset</a>

</form>

<table>
<tr>
    <th>S.No</th>
    <th>Student</th>
    <th>Total Fee</th>
    <th>Paid</th>
    <th>Balance</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {

    $i = 1;

    while($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $i++ ?></td>

    <td><?= htmlspecialchars($row['name']) ?></td>

    <td>₹ <?= number_format($row['total_fee'], 2) ?></td>
    <td>₹ <?= number_format($row['paid_amount'], 2) ?></td>
    <td>₹ <?= number_format($row['balance'], 2) ?></td>

    <td class="<?= $row['status'] == 'Paid' ? 'paid' : 'pending' ?>">
        <?= htmlspecialchars($row['status']) ?>
    </td>

    <td>
        <?php if ($row['status'] != 'Paid') { ?>
            <a class="pay-btn" href="pay_fees.php?id=<?= $row['id'] ?>">💰 Pay</a>
        <?php } else { ?>
            <span class="done">✔ Completed</span>
        <?php } ?>
    </td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="7">No fees records found</td>
</tr>
<?php
}
?>

</table>

</div>

</body>
</html>