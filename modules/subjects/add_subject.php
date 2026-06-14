<?php
include("../../config/db.php");

// Fetch courses
$courses = mysqli_query($conn, "SELECT * FROM courses");

if (isset($_POST['submit'])) {

    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $course_id = intval($_POST['course_id']);

    // ✅ Validation
    if (empty($subject_name) || $course_id <= 0) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        // ✅ Prevent duplicate subject in same course
        $check = mysqli_query($conn, "
            SELECT * FROM subjects 
            WHERE subject_name = '$subject_name' AND course_id = $course_id
        ");

        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Subject already exists for this course');</script>";
        } else {

            mysqli_query($conn, "
                INSERT INTO subjects (subject_name, course_id)
                VALUES ('$subject_name', '$course_id')
            ");

            echo "<script>alert('Subject Added Successfully'); window.location='view_subjects.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/subjects/add_subject.css">
</head>
<body>

<!-- 🔥 BACK BUTTON -->
<a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>

<h2>Add Subject</h2>

<form method="POST">

<input 
    type="text" 
    name="subject_name" 
    placeholder="Subject Name" 
    required
>

<select name="course_id" required>
    <option value="">Select Course</option>
    <?php while($row = mysqli_fetch_assoc($courses)) { ?>
        <option value="<?= $row['id'] ?>">
            <?= htmlspecialchars($row['course_name']) ?>
        </option>
    <?php } ?>
</select>

<button name="submit">Add Subject</button>

</form>

</body>
</html>