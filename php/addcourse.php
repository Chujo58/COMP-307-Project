<?php
// Check if the user is logged in and is a staff member
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'staff') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'comp307project');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $course_tag = $_POST['course_tag'];
    $staff_id = $_POST['staff_id'];

    // Validate staff_id (ensure it's a valid staff ID, e.g., numeric)
    if (!is_numeric($staff_id)) {
        echo "Invalid Staff ID";
        exit();
    }

    // Check if the course tag exists
    $check_course = $conn->prepare("SELECT * FROM course_list WHERE course_tag = ?");
    $check_course->bind_param("s", $course_tag);
    $check_course->execute();
    $course_result = $check_course->get_result();

    if ($course_result->num_rows == 0) {
        echo "Course not found.";
        exit();
    }

    // Insert the staff_id into the course list for the selected course tag
    $stmt = $conn->prepare("INSERT INTO course_list (course_tag, staff_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $course_tag, $staff_id);

    if ($stmt->execute()) {
        echo "Staff ID successfully added to the course.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
