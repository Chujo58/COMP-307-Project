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
try {
    $conn = new SQLite3('../comp307project.db');
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

if ($action === 'add') {
    $query = "SELECT * FROM course_list WHERE course_tag = :course_tag AND course_id = :course_id AND staff_id = :staff_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':course_tag', $course_tag, SQLITE3_TEXT);
    $stmt->bindValue(':course_id', $course_id, SQLITE3_TEXT);
    $stmt->bindValue(':staff_id', $user_id, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($result->fetchArray(SQLITE3_ASSOC)) {
        echo "This course is already added.";
    } else {
        $insert_query = "INSERT INTO course_list (course_tag, course_id, staff_id, course_name) VALUES (:course_tag, :course_id, :staff_id, :course_name)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindValue(':course_tag', $course_tag, SQLITE3_TEXT);
        $insert_stmt->bindValue(':course_id', $course_id, SQLITE3_TEXT);
        $insert_stmt->bindValue(':staff_id', $user_id, SQLITE3_TEXT);
        $insert_stmt->bindValue(':course_name', $course_name, SQLITE3_TEXT);

        if ($insert_stmt->execute()) {
            echo "Course added successfully.";
        } else {
            echo "Failed to add course.";
        }
    }

} elseif ($action === 'remove') {
    // Check if the course exists for this staff before trying to delete it
    $check_query = "SELECT * FROM course_list WHERE course_tag = :course_tag AND course_id = :course_id AND staff_id = :staff_id";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindValue(':course_tag', $course_tag, SQLITE3_TEXT);
    $check_stmt->bindValue(':course_id', $course_id, SQLITE3_TEXT);
    $check_stmt->bindValue(':staff_id', $user_id, SQLITE3_TEXT);
    $check_result = $check_stmt->execute();

    if (!$check_result->fetchArray(SQLITE3_ASSOC)) {
        echo "This course is not in your list.";
    } else {
        $delete_query = "DELETE FROM course_list WHERE course_tag = :course_tag AND course_id = :course_id AND staff_id = :staff_id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindValue(':course_tag', $course_tag, SQLITE3_TEXT);
        $delete_stmt->bindValue(':course_id', $course_id, SQLITE3_TEXT);
        $delete_stmt->bindValue(':staff_id', $user_id, SQLITE3_TEXT);

        if ($delete_stmt->execute()) {
            echo "Course removed successfully.";
        } else {
            echo "Failed to remove course.";
        }
    }
}

$conn->close();
?>
