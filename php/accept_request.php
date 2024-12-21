<?php
//Sophia Zhou
// Connect to the SQLite database

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Connect to the SQLite database
    $conn = new SQLite3('../comp307project.db');
    if (!$conn) {
        die("Internal Server Error");
    }

    // Retrieve input values
    $eventID = $_POST['event_id'] ?? null;

    if ($eventID) {
        // Update the event to change its type to 'booking'
        $query = $conn->prepare("UPDATE events SET event_type = 'booking' WHERE event_id = :event_id");
        $query->bindParam(':event_id', $eventID, SQLITE3_TEXT);

        if ($query->execute()) {
            echo "Request accepted successfully!";
        } else {
            echo "Failed to accept the request. " . $conn->lastErrorMsg();
        }

        $query->close();
    } else {
        echo "Event ID is required.";
    }

    $conn->close();
}
?>
