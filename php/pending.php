<?php
// header('Content-Type: application/json');
// session_start();
// date_default_timezone_set('America/New_York');

// $conn = new mysqli("localhost", "root", "", "comp307project");

// if ($conn->connect_error) {
//     http_response_code(500);
//     echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
//     exit();
// }

// $staffID = $_SESSION['user_id'] ?? null; // Get current staff ID from session

// $current_staff_id = $_SESSION["user_id"] ?? null;
// if ($_SESSION['user_type'] != 'staff') {
//     http_response_code(400);
//     echo json_encode(["error" => "Unauthorized access."]);
//     exit();
// }

// $query = "SELECT event_id, event_name, event_desc, event_start, event_stop FROM events WHERE staff_id = ? AND event_type = 'pending'";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("s", $staffID);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         echo "<div class='pending-request'>";
//         echo "<h3>" . htmlspecialchars($row['event_name']) . "</h3>";
//         echo "<p>Description: " . htmlspecialchars($row['event_desc']) . "</p>";
//         echo "<p>Start: " . htmlspecialchars(date('Y-m-d H:i:s', floor($row['event_start'] / 1000))) . "</p>";
//         echo "<p>End: " . htmlspecialchars(date('Y-m-d H:i:s', floor($row['event_stop'] / 1000))) . "</p>";
//         echo "<button onclick='acceptRequest(\"" . $row['event_id'] . "\")' class='accept-btn'>Accept</button>";
//         echo "<button onclick='denyRequest(\"" . $row['event_id'] . "\")' class='deny-btn'>Deny</button>";
//         echo "</div>";
//     }
// } else {
//     // echo "<p>There are currently no pending requests.</p>";
//     echo "<div class='no-event-body' style='width: 100%;'>There are currently no pending requests.</div>";
// }

// $stmt->close();
// $conn->close();




header('Content-Type: application/json');
session_start();
date_default_timezone_set('America/New_York');

// Connect to SQLite database
$db = new SQLite3('comp307project.db'); // Specify your SQLite database file

if (!$db) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $db->lastErrorMsg()]);
    exit();
}

$staffID = $_SESSION['user_id'] ?? null;

$current_staff_id = $_SESSION["user_id"] ?? null;
if ($_SESSION['user_type'] != 'staff') {
    http_response_code(400);
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

// Prepare the query using SQLite
$query = "SELECT event_id, event_name, event_desc, event_start, event_stop FROM events WHERE staff_id = :staff_id AND event_type = 'pending'";
$stmt = $db->prepare($query);
$stmt->bindValue(':staff_id', $staffID, SQLITE3_TEXT);

// Execute the query and get results
$result = $stmt->execute();

if ($result) {
    $events_found = false;
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $events_found = true;
        echo "<div class='pending-request'>";
        echo "<h3>" . htmlspecialchars($row['event_name']) . "</h3>";
        echo "<p>Description: " . htmlspecialchars($row['event_desc']) . "</p>";
        echo "<p>Start: " . htmlspecialchars(date('Y-m-d H:i:s', floor($row['event_start'] / 1000))) . "</p>";
        echo "<p>End: " . htmlspecialchars(date('Y-m-d H:i:s', floor($row['event_stop'] / 1000))) . "</p>";
        echo "<button onclick='acceptRequest(\"" . $row['event_id'] . "\")' class='accept-btn'>Accept</button>";
        echo "<button onclick='denyRequest(\"" . $row['event_id'] . "\")' class='deny-btn'>Deny</button>";
        echo "</div>";
    }

    if (!$events_found) {
        echo "<div class='no-event-body' style='width: 100%;'>There are currently no pending requests.</div>";
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query execution failed: " . $db->lastErrorMsg()]);
}

$stmt->close();
$db->close();

?>
