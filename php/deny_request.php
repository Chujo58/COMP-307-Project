<?php
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventID = $_POST['event_id'] ?? null;

if ($eventID) {
    $query = "DELETE FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eventID);

    if ($stmt->execute()) {
        echo "Request denied successfully!";
    } else {
        http_response_code(500);
        echo "Failed to deny the request.";
    }

    $stmt->close();
}

$conn->close();
?>