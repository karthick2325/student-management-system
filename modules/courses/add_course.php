<?php
include("../../config/db.php");

if (isset($_POST['submit'])) {

    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    // ✅ Validation
    if (empty($course_name) || empty($duration)) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        // ✅ Prevent duplicate
        $check = mysqli_query($conn, "
            SELECT * FROM courses WHERE course_name = '$course_name'
        ");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Course already exists');</script>";
        } else {

            mysqli_query($conn, "
                INSERT INTO courses (course_name, duration) 
                VALUES ('$course_name', '$duration')
            ");

            echo "<script>alert('Course Added Successfully'); window.location='view_courses.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/courses/add_course.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>Add Course</h2>

<form method="POST">

<input 
    type="text" 
    name="course_name" 
    placeholder="Course Name" 
    required
>

<input 
    type="text" 
    name="duration" 
    placeholder="Duration (e.g., 3 Years)" 
    required
>

<button name="submit">Add Course</button>

</form>

</body>
</html>