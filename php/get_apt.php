<?php
header('Content-Type: application/json');

session_start();

$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$current_date = $_GET['date'];

// Fetch Upcoming Appointments
$current_staff_id = $_SESSION["user_id"] ?? null;

if ($_SESSION['user_type'] != 'staff') {
    http_response_code(400);
    echo json_encode(["error" => "Staff ID is required."]);
    exit();
}

$query = "
    SELECT 
        e.event_desc, 
        e.event_name, 
        e.event_start, 
        e.event_stop, 
        e.student_id,
        e.staff_id,
        e.event_filter,
        u.f_name, 
        u.l_name,
        u.user_id
    FROM events e
    JOIN valid_users u ON e.student_id = u.user_id"; 

$query .= " WHERE e.staff_id ='" . $current_staff_id . "' AND e.event_stop >='" . $current_date . "'";

$query .= "ORDER BY e.event_start ASC LIMIT 3";
// $query->bind_param("ss", $current_staff_id, $current_date); // Bind staff_id and current date parameters
// $query->execute();

// if (!$query->execute()) {
//     http_response_code(500);
//     // echo json_encode(["error" => "Error fetching appointments."]);
//     exit();
// }

//event name, start, stop, student name !

function echoLikeCSV($array){
    $e_name = $array['event_name'];
    $start = $array['event_start'];
    $stop = $array['event_stop'];
    $fname = $array['f_name'];
    $lname = $array['l_name'];
    $filter = $array['event_filter'];
    $desc = $array['event_desc'];
    echo "$e_name,$start,$stop,$fname,$lname,$filter,$desc\n";
}

$result = $conn->query($query);
$events = [];

while ($row = $result->fetch_assoc()) {
    // array_push($events, $row);
    echoLikeCSV($row);
}

// Return events as JSON
// echo json_encode($events);

$conn->close();
exit();
?>
