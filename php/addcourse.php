<?php
session_start(); // Start the session to get user_id from the session
header('Content-Type: text/html; charset=utf-8');

// Ensure user is logged in (session should be set)
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php?error=User is not logged in.");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if required POST data is available
if (!isset($_POST['course_tag']) || !isset($_POST['course_id'])) {
    header("Location: ../index.php?error=Missing course tag or course ID.");
    exit();
}

$course_tag = $_POST['course_tag'];
$course_id = $_POST['course_id'];

// Create DB connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    header("Location: ../index.php?error=Database connection failed: " . $conn->connect_error);
    exit();
}

// Check if the course already exists for the user
$query = "SELECT * FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $course_tag, $course_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Course already exists
    $_SESSION['message'] = 'This course is already added.';
    header("Location: ../index.php?Page=StaffCourses");
    exit();
} else {
    // Insert new course
    $insert_query = "INSERT INTO course_list (course_tag, course_id, staff_id) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ssi", $course_tag, $course_id, $user_id);
    if ($insert_stmt->execute()) {
        $_SESSION['message'] = 'Course added successfully.';
        header("Location: ../index.php?Page=StaffCourses");
        exit();
    } else {
        $_SESSION['message'] = 'Failed to add course.';
        header("Location: ../index.php?Page=StaffCourses");
        exit();
    }
}

$stmt->close();
$insert_stmt->close();
$conn->close();
?>
