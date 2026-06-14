<?php
include("../../config/db.php");

// 🔥 FILTER VALUES
$student_filter = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$course_filter  = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// 🔥 FETCH DROPDOWN DATA
$students_list = mysqli_query($conn, "SELECT id, name FROM students");
$courses_list  = mysqli_query($conn, "SELECT id, course_name FROM courses");

// 🔥 BASE QUERY
$query = "
SELECT 
    students.id AS student_id,
    students.name,
    students.course_id,
    subjects.subject_name,
    subjects.max_marks,
    marks.marks
FROM marks
JOIN students ON marks.student_id = students.id
JOIN subjects ON marks.subject_id = subjects.id
WHERE 1
";

// 🔥 APPLY FILTERS
if ($student_filter > 0) {
    $query .= " AND students.id = '$student_filter'";
}

if ($course_filter > 0) {
    $query .= " AND students.course_id = '$course_filter'";
}

$query .= " ORDER BY students.id";

$result = mysqli_query($conn, $query);

// 🔥 ORGANIZE DATA
$students = [];

while ($row = mysqli_fetch_assoc($result)) {
    $sid = $row['student_id'];

    if (!isset($students[$sid])) {
        $students[$sid] = [
            'name' => $row['name'],
            'subjects' => [],
            'total_obtained' => 0,
            'total_max' => 0
        ];
    }

    $students[$sid]['subjects'][] = [
        'name' => $row['subject_name'],
        'marks' => $row['marks'],
        'max' => $row['max_marks']
    ];

    $students[$sid]['total_obtained'] += $row['marks'];
    $students[$sid]['total_max'] += $row['max_marks'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Results</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/results/view_results.css">
</head>
<body>

<div class="container">

<!-- BACK BUTTON -->
<div class="top-bar">
    <a href="../../dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<h2>Student Results</h2>

<!-- 🔥 FILTER FORM -->
<form method="GET" class="filter-form">

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
<a href="view_results.php" class="reset-btn">Reset</a>

</form>

<table>
<tr>
    <th>S.No</th>
    <th>Student</th>
    <th>Subjects & Marks</th>
    <th>Total</th>
    <th>Percentage</th>
    <th>Grade</th>
</tr>

<?php
if (!empty($students)) {

    $i = 1;

    foreach ($students as $data) {

        $total = $data['total_obtained'];
        $max = $data['total_max'];
        $percentage = ($max > 0) ? ($total / $max) * 100 : 0;

        if ($percentage >= 90) $grade = "A+";
        elseif ($percentage >= 75) $grade = "A";
        elseif ($percentage >= 60) $grade = "B";
        elseif ($percentage >= 50) $grade = "C";
        else $grade = "F";
?>
<tr>
    <td><?= $i++ ?></td>

    <td><?= htmlspecialchars($data['name']) ?></td>

    <td>
        <table class="inner-table">
            <?php foreach ($data['subjects'] as $sub) { ?>
            <tr>
                <td class="sub-name"><?= htmlspecialchars($sub['name']) ?></td>
                <td class="sub-mark"><?= $sub['marks'] ?> / <?= $sub['max'] ?></td>
            </tr>
            <?php } ?>
        </table>
    </td>

    <td><?= $total ?> / <?= $max ?></td>
    <td><?= number_format($percentage, 2) ?>%</td>

    <td class="
        <?= ($grade == 'A+' || $grade == 'A') ? 'grade-a' : '' ?>
        <?= ($grade == 'B') ? 'grade-b' : '' ?>
        <?= ($grade == 'C') ? 'grade-c' : '' ?>
        <?= ($grade == 'F') ? 'grade-f' : '' ?>
    ">
        <?= $grade ?>
    </td>
</tr>
<?php
    }

} else {
?>
<tr>
    <td colspan="6">No results found</td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>