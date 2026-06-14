<?php
include("../../config/db.php");

$query = "
SELECT subjects.*, courses.course_name 
FROM subjects
LEFT JOIN courses ON subjects.course_id = courses.id
ORDER BY subjects.id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Subjects</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/subjects/view_subjects.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>All Subjects</h2>

<a href="add_subject.php" class="add-btn">➕ Add Subject</a><br><br>

<table>
<tr>
    <th>S.No</th> <!-- ✅ Serial -->
    <th>Subject Name</th>
    <th>Course</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {

    $i = 1; // ✅ Counter

    while($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $i++ ?></td>

    <td><?= htmlspecialchars($row['subject_name']) ?></td>
    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="3">No subjects found</td>
</tr>
<?php
}
?>

</table>

</body>
</html>