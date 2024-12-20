<?php
// header('Content-Type: application/json');

// session_start();

// $conn = new mysqli("localhost", "root", "", "comp307project");

// if ($conn->connect_error) {
//     http_response_code(500);
//     echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
//     exit();
// }

// $current_date = $_GET['date'];

// // Fetch Upcoming Appointments
// $current_staff_id = $_SESSION["user_id"] ?? null;

// if ($_SESSION['user_type'] != 'staff') {
//     http_response_code(400);
//     echo json_encode(["error" => "Staff ID is required."]);
//     exit();
// }

// $query = "
//     SELECT 
//         e.event_desc, 
//         e.event_name, 
//         e.event_start, 
//         e.event_stop, 
//         e.student_id,
//         e.staff_id,
//         e.event_filter,
//         u.f_name, 
//         u.l_name,
//         u.user_id,
//         u.user
//     FROM events e
//     JOIN valid_users u ON e.student_id = u.user_id"; 

// $query .= " WHERE e.staff_id ='" . $current_staff_id . "' AND e.event_stop >='" . $current_date . "' AND e.event_type = 'booking'";

// $query .= "ORDER BY e.event_start ASC LIMIT 3";


// function echoLikeCSV($array){
//     $e_name = $array['event_name'];
//     $start = $array['event_start'];
//     $stop = $array['event_stop'];
//     $fname = $array['f_name'];
//     $lname = $array['l_name'];
//     $filter = $array['event_filter'];
//     $desc = $array['event_desc'];
//     $user = $array['user'];
//     echo "$e_name,$start,$stop,$fname,$lname,$filter,$desc,$user\n";
// }

// $result = $conn->query($query);
// $events = [];

// while ($row = $result->fetch_assoc()) {
//     // array_push($events, $row);
//     echoLikeCSV($row);
// }

// // Return events as JSON
// // echo json_encode($events);

// $conn->close();
// exit();



header('Content-Type: application/json');

session_start();

// Connect to SQLite database
$conn = new SQLite3('../comp307project.db'); // Assuming the database file is named 'comp307project.db'
if (!$conn) {
    die("Internal Server Error");
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
        u.user_id,
        u.user
    FROM events e
    JOIN valid_users u ON e.student_id = u.user_id
    WHERE e.staff_id = :staff_id AND e.event_stop >= :current_date AND e.event_type = 'booking'
    ORDER BY e.event_start ASC LIMIT 3
";

// Prepare and bind parameters
$stmt = $conn->prepare($query);
$stmt->bindValue(':staff_id', $current_staff_id, SQLITE3_INTEGER);
$stmt->bindValue(':current_date', $current_date, SQLITE3_TEXT);

// Execute the query
$result = $stmt->execute();

// Function to echo the data in CSV format
function echoLikeCSV($array){
    $e_name = $array['event_name'];
    $start = $array['event_start'];
    $stop = $array['event_stop'];
    $fname = $array['f_name'];
    $lname = $array['l_name'];
    $filter = $array['event_filter'];
    $desc = $array['event_desc'];
    $user = $array['user'];
    echo "$e_name,$start,$stop,$fname,$lname,$filter,$desc,$user\n";
}

// Fetch and output events
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echoLikeCSV($row);
}

// Close the database connection
$conn->close();
exit();
?>
