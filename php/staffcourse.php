<?php
// Rachel Shi
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

$query = "SELECT DISTINCT course_tag, course_id, course_name FROM course_list WHERE staff_id = :staff_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':staff_id', $current_user_id, SQLITE3_TEXT);

$result = $stmt->execute();

function echoLikeCSV($array) {
    $course_tag = $array['course_tag'];
    $course_id = $array['course_id'];
    $course_name = $array['course_name'];
    echo "$course_tag,$course_id,$course_name\n";
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
