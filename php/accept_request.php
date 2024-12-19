<?php
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventID = $_POST['event_id'] ?? null;

if ($eventID) {
    $query = "UPDATE events SET event_type = 'booking' WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eventID);

    if ($stmt->execute()) {
        echo "Request accepted successfully!";
    } else {
        http_response_code(500);
        echo "Failed to accept the request.";
    }

    $stmt->close();
}

$conn->close();
?>
