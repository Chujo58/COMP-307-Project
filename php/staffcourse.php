<?php
session_start();

// Ensure user is logged in and has a valid session
if (!isset($_SESSION["user_id"])) {
    echo "Error: User is not logged in.";
    exit();
}

$current_user_id = $_SESSION["user_id"];

// Create DB connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    http_response_code(500);
    echo "Error: Database connection failed - " . $conn->connect_error;
    exit();
}

// Fetch courses for the current logged-in user
$query = "SELECT course_tag, course_id FROM course_list WHERE staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Function to output course details in CSV-like format
function echoLikeCSV($array) {
    $course_tag = $array['course_tag'];
    $course_id = $array['course_id'];
    echo "$course_tag,$course_id\n";
}

// If courses are found, print them in CSV format
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echoLikeCSV($row);
    }
} else {
    echo "No courses found for this user.";
}

$stmt->close();
$conn->close();
exit();
?>
