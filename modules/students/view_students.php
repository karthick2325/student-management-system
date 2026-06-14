<?php
include("../../config/db.php");

// Search logic
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "SELECT students.*, courses.course_name 
              FROM students 
              LEFT JOIN courses ON students.course_id = courses.id
              WHERE students.name LIKE '%$search%' 
              OR courses.course_name LIKE '%$search%'";
} else {
    $query = "SELECT students.*, courses.course_name 
              FROM students 
              LEFT JOIN courses ON students.course_id = courses.id";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/students/view_students.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>All Students</h2>

<!-- 🔍 SEARCH FORM -->
<form method="GET" class="search-box">
    <input 
        type="text" 
        name="search" 
        placeholder="Search by name or course"
        value="<?= htmlspecialchars($search) ?>"
    >
    <button type="submit">Search</button>
    <a href="view_students.php" class="reset-btn">Reset</a>
</form>

<br>

<a href="add_student.php" class="add-btn">➕ Add New Student</a><br><br>

<table>
<tr>
    <th>S.No</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Course</th>
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
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['phone']) ?></td>

    <td><?= htmlspecialchars($row['course_name'] ?? 'N/A') ?></td>

    <td>
        <!-- ✅ EDIT BUTTON -->
        <a class="action-btn edit-btn" 
           href="edit_student.php?id=<?= $row['id'] ?>">
           ✏ Edit
        </a> 
        
        <!-- ✅ DELETE BUTTON -->
        <a class="action-btn delete-btn" 
           href="delete_student.php?id=<?= $row['id'] ?>" 
           onclick="return confirm('Are you sure you want to delete this student?')">
           🗑 Delete
        </a>
    </td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="6">No students found</td>
</tr>
<?php
}
?>

</table>

</body>
</html>