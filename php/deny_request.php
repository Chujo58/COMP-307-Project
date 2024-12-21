<?php
// Sophia Zhou
    $conn = new SQLite3('../comp307project.db');  // Replace with the actual path to your SQLite3 database file
    if (!$conn) {
        die("Internal Server Error");
    }
    
    $eventID = $_POST['event_id'] ?? null;

    if ($eventID) {
        $query = "DELETE FROM events WHERE event_id = :event_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':event_id', $eventID, SQLITE3_TEXT);

        $result = $stmt->execute();

        if ($result) {
            echo "Request denied successfully!";
        } else {
            http_response_code(500);
            echo "Failed to deny the request.";
        }

        $stmt->close();
    }

$conn->close();
?>
