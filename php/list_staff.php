<?php
// Sophia Zhou
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
$query = "SELECT DISTINCT vu.l_name, vu.f_name, vu.user_id FROM course_list cl INNER JOIN valid_users vu ON cl.staff_id = vu.user_id WHERE cl.course_id = :course_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':course_id', $courseID, SQLITE3_TEXT);

//TODO change this
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
        $event = 'redirectToCalendar(\'' . $row['user_id'] . '\');';
        echo "<div class='staff-card' onclick=\"" . $event . "\">";
        echo "<h3>" . htmlspecialchars($row['f_name']) . " " . htmlspecialchars($row['l_name']) . "</h3>";
        echo "</div>";
    }
}

$conn->close();
?>
