<?php
// $conn = new mysqli("localhost", "root", "", "comp307project");
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// $eventID = $_POST['event_id'] ?? null;

// if ($eventID) {
//     $query = "DELETE FROM events WHERE event_id = ?";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("s", $eventID);

//     if ($stmt->execute()) {
//         echo "Request denied successfully!";
//     } else {
//         http_response_code(500);
//         echo "Failed to deny the request.";
//     }

//     $stmt->close();
// }

// $conn->close();



try {
    $conn = new SQLite3('comp307project.db');  // Replace with the actual path to your SQLite3 database file

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

} catch (Exception $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
}

$conn->close();
?>
