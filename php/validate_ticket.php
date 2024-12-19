<?php 
// if ($_SERVER['REQUEST_METHOD'] == "POST"){
//     $conn = new mysqli("localhost", "root", "", "comp307project");

//     if ($conn->connect_error) {
//         die("Internal Server Error: ". $conn->connect_error);
//     }

//     $query_ticket = "SELECT exp_date FROM tickets WHERE ticket_id='" . $_POST['ticket'] . "'";
//     $ticket_res = $conn->query($query_ticket);
//     if ($ticket_res->num_rows != 0 && date("Y-m-d") < $ticket_res->fetch_assoc()["exp_date"]) {
//         print("Valid");
//     }
//     else {
//         print("Not valid");
//     }
// }

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