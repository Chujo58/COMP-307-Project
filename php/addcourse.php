<?php
session_start(); // Start the session to get user_id from the session
header('Content-Type: application/json');

// Ensure user is logged in (session should be set)
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "User is not logged in."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$course_tag = $_POST['course_tag'];
$course_id = $_POST['course_id'];

// Create DB connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
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
    echo "<script>document.getElementById('message').innerText = 'This course is already added.';</script>";
} else {
    // Insert new course
    $insert_query = "INSERT INTO course_list (course_tag, course_id, staff_id) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ssi", $course_tag, $course_id, $user_id);
    if ($insert_stmt->execute()) {
        echo "<script>document.getElementById('message').innerText = 'Course added successfully.';</script>";
    } else {
        echo "<script>document.getElementById('message').innerText = 'Failed to add course.';</script>";
    }
}

$stmt->close();
$insert_stmt->close();
$conn->close();
?>
