<?php
include("../../config/db.php");

// Fetch courses
$courses = mysqli_query($conn, "SELECT * FROM courses");

// Add student
if (isset($_POST['submit'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course_id = intval($_POST['course_id']);

    // ✅ Validation
    if (empty($name) || empty($email) || empty($phone) || $course_id <= 0) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        // ✅ Check duplicate email
        $check = mysqli_query($conn, "SELECT * FROM students WHERE email = '$email'");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email already exists');</script>";
        } else {

            mysqli_query($conn, "
                INSERT INTO students (name, email, phone, course_id)
                VALUES ('$name','$email','$phone','$course_id')
            ");

            echo "<script>alert('Student Added Successfully'); window.location='view_students.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/students/add_student.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>Add Student</h2>

<form method="POST">

<input 
    type="text" 
    name="name" 
    placeholder="Name" 
    required
>

<input 
    type="email" 
    name="email" 
    placeholder="Email" 
    required
>

<input 
    type="text" 
    name="phone" 
    placeholder="Phone" 
    pattern="[0-9]{10}" 
    title="Enter 10 digit number"
    required
>

<!-- Course Dropdown -->
<select name="course_id" required>
    <option value="">Select Course</option>
    <?php while($row = mysqli_fetch_assoc($courses)) { ?>
        <option value="<?= $row['id'] ?>">
            <?= htmlspecialchars($row['course_name']) ?>
        </option>
    <?php } ?>
</select>

<button name="submit">Add Student</button>

</form>

</body>
</html>