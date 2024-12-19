<?php
session_start();
// Database Connection
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventID = $_POST['event_id'] ?? '';

if(empty($eventID)){
    echo "<p>No event selected.</p>";
    exit();
}

function echoLikeCSV($array, $staff, $student){
    echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . ',' . $staff . ',' . $student . ',' . $_SESSION['user_type'] . '\n';
}

$query = "SELECT event_name, event_desc, event_start, event_stop, event_filter, event_id, staff_id, student_id FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eventID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0){
    echo "<p>The event selected doesn't exist or has been deleted.</p>";
} else {
    while ($row = $result->fetch_assoc()){
        $query_staid = "SELECT f_name, l_name from valid_users WHERE user_id='" . $row['staff_id'] . "'";
        $query_stud = "SELECT f_name, l_name from valid_users WHERE user_id='" . $row['student_id'] . "'";

        $staff = $conn->query($query_staid)->fetch_assoc();
        if ($staff != null){
            $staff = $staff['f_name'] . ' ' . $staff['l_name'];
        } else {
            $staff = '';
        }            

        $student = $conn->query($query_stud)->fetch_assoc();
        if ($student != null){
            $student = $student['f_name'] . ' ' . $student['l_name'];  
        } else {
            $student = '';
        }

        echoLikeCSV($row, $staff, $student);
    }
} 

?>