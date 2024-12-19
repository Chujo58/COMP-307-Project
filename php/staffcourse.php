<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "Error: User is not logged in.";
    exit();
}

$current_user_id = $_SESSION["user_id"];

$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    http_response_code(500);
    echo "Error: Database connection failed - " . $conn->connect_error;
    exit();
}

$query = "SELECT course_tag, course_id FROM course_list WHERE staff_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

function echoLikeCSV($array) {
    $course_tag = $array['course_tag'];
    $course_id = $array['course_id'];
    echo "$course_tag,$course_id\n";
}

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
