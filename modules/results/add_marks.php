<?php
include("../../config/db.php");

// Fetch students
$students = mysqli_query($conn, "
    SELECT students.*, courses.course_name 
    FROM students 
    LEFT JOIN courses ON students.course_id = courses.id
");

// Fetch subjects
$subjects_result = mysqli_query($conn, "SELECT id, subject_name, max_marks FROM subjects");

// Store subjects
$subjects = [];
while ($row = mysqli_fetch_assoc($subjects_result)) {
    $subjects[$row['id']] = $row;
}

// Save marks
if (isset($_POST['submit'])) {

    $student_id = intval($_POST['student_id']);

    // Prevent duplicate
    $check = mysqli_query($conn, "SELECT 1 FROM marks WHERE student_id = $student_id LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Marks already added for this student');</script>";
    } else {

        mysqli_begin_transaction($conn);

        try {

            foreach ($_POST['marks'] as $subject_id => $mark) {

                $subject_id = intval($subject_id);
                $mark = floatval($mark);

                if (!isset($subjects[$subject_id])) continue;

                $max_marks = floatval($subjects[$subject_id]['max_marks']);

                if ($mark < 0) $mark = 0;
                if ($mark > $max_marks) $mark = $max_marks;

                mysqli_query($conn, "
                    INSERT INTO marks (student_id, subject_id, marks)
                    VALUES ('$student_id', '$subject_id', '$mark')
                ");
            }

            mysqli_commit($conn);

            echo "<script>alert('Result Saved Successfully'); window.location='view_results.php';</script>";

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Marks</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/results/add_marks.css">
</head>
<body>

<div class="container">

<!-- ✅ FIXED BACK BUTTON POSITION -->
<div class="top-bar">
    <a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<form method="POST">

<h2>Add Student Marks</h2>

<select name="student_id" required>
    <option value="">Select Student</option>
    <?php while($row = mysqli_fetch_assoc($students)) { ?>
        <option value="<?= $row['id'] ?>">
            <?= htmlspecialchars($row['name']) ?>
        </option>
    <?php } ?>
</select>

<div class="subjects">

<?php foreach ($subjects as $sub) { ?>
    <div class="subject-row">
        <label>
            <?= htmlspecialchars($sub['subject_name']) ?> 
            (Max: <?= $sub['max_marks'] ?>)
        </label>
        <input type="number" 
               name="marks[<?= $sub['id'] ?>]" 
               class="mark-input"
               max="<?= $sub['max_marks'] ?>"
               min="0"
               placeholder="0 - <?= $sub['max_marks'] ?>"
               required>
    </div>
<?php } ?>

</div>

<div class="result-box">
    <p id="total">Total: 0</p>
    <p id="percentage">Percentage: 0%</p>
</div>

<button name="submit">Save Result</button>

</form>

</div>

<script>
const inputs = document.querySelectorAll(".mark-input");

inputs.forEach(input => {
    input.addEventListener("input", calculate);
});

function calculate() {
    let total = 0;
    let maxTotal = 0;

    inputs.forEach(input => {
        let val = parseFloat(input.value) || 0;
        let max = parseFloat(input.getAttribute("max")) || 100;

        if (val < 0) val = 0;
        if (val > max) val = max;

        total += val;
        maxTotal += max;
    });

    let percentage = maxTotal > 0 ? (total / maxTotal) * 100 : 0;

    document.getElementById("total").innerText = "Total: " + total;
    document.getElementById("percentage").innerText =
        "Percentage: " + percentage.toFixed(2) + "%";
}
</script>

</body>
</html>