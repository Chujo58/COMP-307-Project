<?php
// Database Connection using SQLite3
$conn = new SQLite3('../comp307project.db');

if (!$conn) {
    die("Connection failed: Unable to connect to SQLite database.");
}

// Retrieve the course ID
$courseID = $_GET['course_id'] ?? '';
if (empty($courseID)) {
    echo "<p>No course selected!</p>";
    exit();
}

// Prepare the query
$query = "SELECT staff_id, course_name FROM course_list WHERE course_id = :course_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':course_id', $courseID, SQLITE3_TEXT);

$count_query = "SELECT COUNT(*) FROM course_list WHERE course_id = :course_id";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bindValue(':course_id',$courseID, SQLITE3_TEXT);

// Execute the query
$result = $stmt->execute();
$count_result = $count_stmt->execute();

if (!$result) {
    die("Query failed: " . $conn->lastErrorMsg());
}

// Display staff details
if ($count_result->fetchArray(SQLITE3_NUM)[0] == 0) {
    echo "<p>No staff associated with this course!</p>";
} else {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "<div class='staff-card'>";
        echo "<h3>Staff ID: " . htmlspecialchars($row['staff_id']) . "</h3>";
        echo "<p>Name: " . htmlspecialchars($row['course_name']) . "</p>";
        echo "</div>";
    }
}

$conn->close();
?>
