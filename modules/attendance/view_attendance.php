<?php
include("../../config/db.php");

// 🔥 FILTER VALUES
$date_filter    = isset($_GET['date']) ? $_GET['date'] : "";
$student_filter = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$course_filter  = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// 🔥 DROPDOWN DATA
$students_list = mysqli_query($conn, "SELECT id, name FROM students");
$courses_list  = mysqli_query($conn, "SELECT id, course_name FROM courses");

// 🔥 BASE QUERY
$query = "
SELECT attendance.*, students.name, courses.course_name 
FROM attendance
LEFT JOIN students ON attendance.student_id = students.id
LEFT JOIN courses ON students.course_id = courses.id
WHERE 1
";

// 🔥 APPLY FILTERS
if (!empty($date_filter)) {
    $date_filter = mysqli_real_escape_string($conn, $date_filter);
    $query .= " AND attendance.date = '$date_filter'";
}

if ($student_filter > 0) {
    $query .= " AND attendance.student_id = '$student_filter'";
}

if ($course_filter > 0) {
    $query .= " AND students.course_id = '$course_filter'";
}

$query .= " ORDER BY attendance.date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/attendance/view_attendance.css">
</head>
<body>

<div class="container">

<!-- ✅ BACK BUTTON FIX -->
<div class="top-bar">
    <a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<h2>Attendance Report</h2>

<!-- 🔥 FILTER FORM -->
<form method="GET" class="filter-form">

<input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">

<select name="student_id">
    <option value="">All Students</option>
    <?php while($s = mysqli_fetch_assoc($students_list)) { ?>
        <option value="<?= $s['id'] ?>" <?= ($student_filter == $s['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['name']) ?>
        </option>
    <?php } ?>
</select>

<select name="course_id">
    <option value="">All Courses</option>
    <?php while($c = mysqli_fetch_assoc($courses_list)) { ?>
        <option value="<?= $c['id'] ?>" <?= ($course_filter == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['course_name']) ?>
        </option>
    <?php } ?>
</select>

<button type="submit">Filter</button>
<a href="view_attendance.php" class="reset-btn">Reset</a>

</form>

<table>
<tr>
    <th>S.No</th>
    <th>Date</th>
    <th>Student</th>
    <th>Course</th>
    <th>Status</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {

    $i = 1;

    while($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($row['date']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>

    <td class="<?= $row['status'] == 'Present' ? 'present' : 'absent' ?>">
        <?= htmlspecialchars($row['status']) ?>
    </td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="5">No attendance records found</td>
</tr>
<?php
}
?>

</table>

</div>

</body>
</html>