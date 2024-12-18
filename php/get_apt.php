<?php
header('Content-Type: application/json');

session_start();

$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$current_date = date('Y-m-d H:i:s'); 

// Fetch Upcoming Appointments
$current_staff_id = $_SESSION["user_id"] ?? null;

if (!$current_staff_id) {
    http_response_code(400);
    echo json_encode(["error" => "Staff ID is required."]);
    exit();
}

$query = $conn->prepare("
    SELECT 
        e.event_desc, 
        e.event_name, 
        e.event_start, 
        e.event_stop, 
        e.student_id, 
        u.fname, 
        u.lname
    FROM events e
    JOIN valid_users u ON e.student_id = u.student_id
    WHERE e.staff_id = ? AND e.event_start > ? 
    ORDER BY e.event_start ASC
    LIMIT 3
"); 

$query->bind_param("ss", $current_staff_id, $current_date); // Bind staff_id and current date parameters

if (!$query->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Error fetching appointments."]);
    exit();
}

$result = $query->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Return events as JSON
echo json_encode($events);

$query->close();
$conn->close();
exit();
?>
