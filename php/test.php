<?php
// Chloé Legué
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
