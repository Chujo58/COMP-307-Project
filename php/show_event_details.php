<?php
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

function echoLikeCSV($array){
    echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . '\n';
}

$query = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eventID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0){
    echo "<p>The event selected doesn't exist or has been deleted.</p>";
} else {
    while ($row = $result->fetch_assoc()){
        // echo `<div class='event_name'>$event_name</div>$event_desc`;
        echoLikeCSV($row);
    }
} 

?>