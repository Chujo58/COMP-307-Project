<?php
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!";
}
?>
