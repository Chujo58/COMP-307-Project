<?php
// session_start();

// if (!isset($_SESSION["user_id"])) {
//     echo "Error: User is not logged in.";
//     exit();
// }

// $current_user_id = $_SESSION["user_id"];

// $conn = new mysqli("localhost", "root", "", "comp307project");

// if ($conn->connect_error) {
//     http_response_code(500);
//     echo "Error: Database connection failed - " . $conn->connect_error;
//     exit();
// }

// $query = "SELECT DISTINCT course_tag, course_id FROM course_list WHERE staff_id = ?";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("s", $current_user_id);
// $stmt->execute();
// $result = $stmt->get_result();

// function echoLikeCSV($array) {
//     $course_tag = $array['course_tag'];
//     $course_id = $array['course_id'];
//     echo "$course_tag,$course_id\n";
// }

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         echoLikeCSV($row);
//     }
// } else {
//     echo "No courses found for this user.";
// }

// $stmt->close();
// $conn->close();
// exit();
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "Error: User is not logged in.";
    exit();
}

$current_user_id = $_SESSION["user_id"];

$conn = new SQLite3('../comp307project.db');
if (!$conn) {
    die("Internal Server Error: Unable to establish a database connection.");
}

$query = "SELECT DISTINCT course_tag, course_id FROM course_list WHERE staff_id = :staff_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':staff_id', $current_user_id, SQLITE3_TEXT);

$result = $stmt->execute();

function echoLikeCSV($array) {
    $course_tag = $array['course_tag'];
    $course_id = $array['course_id'];
    echo "$course_tag,$course_id\n";
}

$found = false;

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $found = true;
    echoLikeCSV($row);
}

if (!$found) {
    echo "No courses found for this user.";
}

$stmt->close();
$conn->close();
exit();

?>
