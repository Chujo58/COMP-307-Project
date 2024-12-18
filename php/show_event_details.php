<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventID = $_POST['event_id'] ?? '';
if(empty($eventID)){
    echo "<p>No event selected.</p>";
}

$query = "SELECT * WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $eventID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0){
    echo "<p>The event selected doesn't exist or has been deleted.</p>";
} else {
    while ($row = $result->fetch_assoc()){
        
    }
} 

?>