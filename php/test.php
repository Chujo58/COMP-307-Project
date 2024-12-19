<?php
// $conn = new mysqli("localhost", "root", "", "comp307project");

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } else {
//     echo "Database connection successful!";
// }
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Connect to the SQLite database
    $conn = new SQLite3('../comp307project.db');

    if (!$conn) {
        die("Internal Server Error: Unable to establish a database connection.");
    } else {
        echo "Database connection successful!";
    }

    // Close the database connection
    $conn->close();
}
?>
