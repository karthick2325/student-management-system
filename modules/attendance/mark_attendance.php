<?php
include("../../config/db.php");

// Fetch students with course
$students = mysqli_query($conn, "
    SELECT students.*, courses.course_name 
    FROM students 
    LEFT JOIN courses ON students.course_id = courses.id
");

// Save attendance
if (isset($_POST['submit'])) {

    $date = mysqli_real_escape_string($conn, $_POST['date']);

    foreach ($_POST['status'] as $student_id => $status) {

        $student_id = intval($student_id);
        $status = mysqli_real_escape_string($conn, $status);

        // Prevent duplicate
        $check = mysqli_query($conn, "
            SELECT * FROM attendance 
            WHERE student_id = '$student_id' AND date = '$date'
        ");

        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "
                INSERT INTO attendance (student_id, date, status)
                VALUES ('$student_id', '$date', '$status')
            ");
        }
    }

    echo "<script>alert('Attendance Saved Successfully'); window.location='view_attendance.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/attendance/mark_attendance.css">
</head>
<body>

<div class="container">

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<form method="POST">

<h2>Mark Attendance</h2>

<!-- Date -->
<input type="date" name="date" value="<?= date('Y-m-d') ?>" required>

<!-- 🔥 Mark All Present -->
<button type="button" onclick="markAllPresent()" class="btn-present">
    Mark All Present
</button>

<table>
<tr>
    <th>S.No</th> <!-- ✅ Serial Number -->
    <th>Name</th>
    <th>Course</th>
    <th>Status</th>
</tr>

<?php 
$i = 1; // ✅ counter
while($row = mysqli_fetch_assoc($students)) { ?>
<tr>
    <td><?= $i++ ?></td> <!-- ✅ Serial Number -->
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
    <td>
        <select name="status[<?= $row['id'] ?>]">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
    </td>
</tr>
<?php } ?>

</table>

<button name="submit">Save Attendance</button>

</form>

</div>

<!-- 🔥 JS -->
<script>
function markAllPresent() {
    document.querySelectorAll("select").forEach(select => {
        select.value = "Present";
    });
}
</script>

</body>
</html>