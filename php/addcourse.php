<?php
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1);

// Ensure user is logged in (session should be set)
if (!isset($_SESSION["user_id"])) {
    echo "error=User is not logged in.";
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if required POST data is available
if (!isset($_POST['course_tag']) || !isset($_POST['course_id']) || !isset($_POST['action'])) {
    echo "error=Missing required data.";
    exit();
}

$course_tag = $_POST['course_tag'];
$course_id = $_POST['course_id'];
$action = $_POST['action'];  // action could be 'add' or 'remove'

// Create DB connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    echo "error=Database connection failed: " . $conn->connect_error;
    exit();
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($delete_stmt)) {
    $delete_stmt->close();
}

// Check if the course already exists for the user
if ($action === 'add') {
    $query = "SELECT * FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $course_tag, $course_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "error=This course is already added.";
    } else {
   
        $insert_query = "INSERT INTO course_list (course_tag, course_id, staff_id) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sss", $course_tag, $course_id, $user_id);
        if ($insert_stmt->execute()) {
            echo "success=Course added successfully.";
        } else {
            echo "error=Failed to add course.";
        }
    }
} elseif ($action === 'remove') {

    $delete_query = "DELETE FROM course_list WHERE course_tag = ? AND course_id = ? AND staff_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("sss", $course_tag, $course_id, $user_id);
    if ($delete_stmt->execute()) {
        echo "success=Course removed successfully.";
    } else {
        echo "error=Failed to remove course.";
    }
}

$stmt->close();
$delete_stmt->close();
$conn->close();
?>
