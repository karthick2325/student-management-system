<?php
include("../../config/db.php");

// Get ID
$id = $_GET['id'];

// Delete query
$query = "DELETE FROM students WHERE id=$id";

if (mysqli_query($conn, $query)) {
    header("Location: view_students.php");
} else {
    echo "Error deleting record";
}
?>