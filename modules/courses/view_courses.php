<?php
include("../../config/db.php");

$result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Courses</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/courses/view_courses.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>All Courses</h2>

<a href="add_course.php" class="add-btn">➕ Add Course</a><br><br>

<table>
<tr>
    <th>S.No</th> <!-- ✅ Serial Number -->
    <th>Course Name</th>
    <th>Duration</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {

    $i = 1; // ✅ Counter

    while($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $i++ ?></td> <!-- ✅ Serial Number -->
    <td><?= htmlspecialchars($row['course_name']) ?></td>
    <td><?= htmlspecialchars($row['duration']) ?></td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="3">No courses found</td>
</tr>
<?php
}
?>

</table>

</body>
</html>