<?php 
// Ling Jie Chen
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Connect to the SQLite database
    $conn = new SQLite3('../comp307project.db');
    if (!$conn) {
        die("Internal Server Error");
    }

    // Retrieve and sanitize input values
    $ticketID = $_POST['ticket'] ?? null;

    if ($ticketID) {
        // Query to check if the ticket is valid
        $query = $conn->prepare("SELECT exp_date FROM tickets WHERE ticket_id = :ticket_id");
        $query->bindParam(':ticket_id', $ticketID, SQLITE3_TEXT);

        $result = $query->execute();

        if ($result) {
            $row = $result->fetchArray(SQLITE3_ASSOC);
            if ($row && date("Y-m-d") < $row["exp_date"]) {
                echo "Valid";
            } else {
                echo "Not valid";
            }
        } else {
            echo "Not valid";
        }

        $query->close();
    } else {
        echo "Ticket ID is required.";
    }

    $conn->close();
}
?>