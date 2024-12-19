<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure user is logged in (session should be set)
if (!isset($_SESSION["user_id"])) {
    echo "User is not logged in.";
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if required POST data is available
if (!isset($_POST['course_tag']) || !isset($_POST['course_id']) || !isset($_POST['action']) || !isset($_POST['course_name'])) {
    echo "Missing required data.";
    exit();
}

$course_tag = $_POST['course_tag'];
$course_id = $_POST['course_id'];
$course_name = $_POST['course_name'];
$action = $_POST['action'];  // action could be 'add' or 'remove'

// Create DB connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    echo "Database connection failed: " . $conn->connect_error;
    exit();
}

if ($action === 'add') {
    $query = "SELECT * FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $course_tag, $course_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This course is already added.";
    } else {
        $insert_query = "INSERT INTO course_list (course_tag, course_id, staff_id, course_name) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ssss", $course_tag, $course_id, $user_id, $course_name);
        if ($insert_stmt->execute()) {
            echo "Course added successfully.";
        } else {
            echo "Failed to add course.";
        }
        $insert_stmt->close();
    }
    $stmt->close();
    
} elseif ($action === 'remove') {
    // Check if the course exists for this staff before trying to delete it
    $check_query = "SELECT * FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("sss", $course_tag, $course_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        // If the course does not exist for this staff, return an appropriate message
        echo "This course is not in your list.";
    } else {
        // If the course exists, proceed with the deletion
        $delete_query = "DELETE FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("sss", $course_tag, $course_id, $user_id);
        if ($delete_stmt->execute()) {
            echo "Course removed successfully.";
        } else {
            echo "Failed to remove course.";
        }
        $delete_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
