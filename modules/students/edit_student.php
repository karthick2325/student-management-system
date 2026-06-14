<?php
include("../../config/db.php");

// ✅ Safe ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch student
$result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Invalid Student ID");
}

// Fetch courses
$courses = mysqli_query($conn, "SELECT * FROM courses");

// Update student
if (isset($_POST['update'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course_id = intval($_POST['course_id']);

    // ✅ Validation
    if (empty($name) || empty($email) || empty($phone) || $course_id <= 0) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        // ✅ Check duplicate email (exclude current student)
        $check = mysqli_query($conn, "
            SELECT * FROM students 
            WHERE email = '$email' AND id != $id
        ");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email already exists');</script>";
        } else {

            mysqli_query($conn, "
                UPDATE students 
                SET name='$name', email='$email', phone='$phone', course_id='$course_id' 
                WHERE id=$id
            ");

            echo "<script>alert('Student Updated Successfully'); window.location='view_students.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/students/edit_student.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>Edit Student</h2>

<form method="POST">

<input 
    type="text" 
    name="name" 
    value="<?= htmlspecialchars($row['name']) ?>" 
    required
>

<input 
    type="email" 
    name="email" 
    value="<?= htmlspecialchars($row['email']) ?>" 
    required
>

<input 
    type="text" 
    name="phone" 
    value="<?= htmlspecialchars($row['phone']) ?>" 
    pattern="[0-9]{10}" 
    required
>

<!-- ✅ Course Dropdown -->
<select name="course_id" required>
    <option value="">Select Course</option>
    <?php while($c = mysqli_fetch_assoc($courses)) { ?>
        <option value="<?= $c['id'] ?>" 
            <?= ($c['id'] == $row['course_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['course_name']) ?>
        </option>
    <?php } ?>
</select>

<button name="update">Update Student</button>

</form>

</body>
</html>