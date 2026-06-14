<?php
session_start();
include("config/db.php");

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Admin name
$admin_name = htmlspecialchars($_SESSION['admin'] ?? 'Admin');

// Safe count function
function getCount($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table);
    $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");

    if ($query) {
        $data = mysqli_fetch_assoc($query);
        return $data['total'] ?? 0;
    }
    return 0;
}

// Dynamic Results Count (marks table)
$resultQuery = mysqli_query($conn, "SELECT COUNT(DISTINCT student_id) AS total FROM marks");
$resultData = mysqli_fetch_assoc($resultQuery);
$total_results = $resultData['total'] ?? 0;

// Other counts
$total_students   = getCount($conn, "students");
$total_courses    = getCount($conn, "courses");
$total_subjects   = getCount($conn, "subjects");
$total_attendance = getCount($conn, "attendance");
$total_fees       = getCount($conn, "fees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management</title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="links">
    <a class="active" href="dashboard.php">🏠 Dashboard</a>

    <a href="modules/students/view_students.php">📋 Students</a>
    <a href="modules/courses/view_courses.php">📚 Courses</a>
    <a href="modules/subjects/view_subjects.php">📘 Subjects</a>

    <a href="modules/attendance/mark_attendance.php">📝 Attendance</a>
    <a href="modules/attendance/view_attendance.php">📊 View Attendance</a>

    <!-- Results -->
    <a href="modules/results/add_marks.php">📝 Add Results</a>
    <a href="modules/results/view_results.php">📊 View Results</a>

    <!-- Fees -->
    <a href="modules/fees/add_fees.php">💰 Add Fees</a>
    <a href="modules/fees/view_fees.php">📊 View Fees</a>

    <a href="logout.php">🚪 Logout</a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

<h1>Welcome, <?= $admin_name ?> 👋</h1>

<div class="dashboard-container">

    <div class="card blue">
        <h2>Students</h2>
        <p><?= $total_students ?></p>
    </div>

    <div class="card green">
        <h2>Courses</h2>
        <p><?= $total_courses ?></p>
    </div>

    <div class="card pink">
        <h2>Subjects</h2>
        <p><?= $total_subjects ?></p>
    </div>

    <div class="card orange">
        <h2>Attendance</h2>
        <p><?= $total_attendance ?></p>
    </div>

    <div class="card purple">
        <h2>Results</h2>
        <p><?= $total_results ?></p>
    </div>

    <div class="card red">
        <h2>Fees</h2>
        <p><?= $total_fees ?></p>
    </div>

</div>

</div>

</body>
</html>