<?php
// Rachel Shi
header('Content-Type: application/json');

session_start();


$conn = new SQLite3('../comp307project.db');
if (!$conn) {
    die("Internal Server Error");
}

$current_date = $_GET['date'];

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

$stmt = $conn->prepare($query);
$stmt->bindValue(':staff_id', $current_staff_id, SQLITE3_TEXT);
$stmt->bindValue(':current_date', $current_date, SQLITE3_TEXT);

$result = $stmt->execute();

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

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echoLikeCSV($row);
}


$conn->close();
exit();
?>
