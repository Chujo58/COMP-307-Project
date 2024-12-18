<?php
session_start();

// Check for cookies
try {
    $conn = new SQLite3('../comp307project.db');

    if (!$conn) {
        die("Internal Server Error: Unable to connect to the SQLite database.");
    }

    if (!isset($_COOKIE['ticket_id'])) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "failed", "message" => "Please log in."]);
        exit();
    }

    $ticket_id = $_COOKIE['ticket_id'];

    // Use a prepared statement to select user data based on ticket_id
    $stmt = $conn->prepare("SELECT user, user_id, exp_date FROM valid_users WHERE ticket_id = :ticket_id");
    $stmt->bindValue(':ticket_id', $ticket_id, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    // Fetch user data
    $user_data = $result->fetchArray(SQLITE3_ASSOC);

    if (!$user_data || time() > $user_data['exp_date'] || is_null($user_data['exp_date'])) {
        // Check if expired
        setcookie('ticket_id', '', time() - 3600, '/');
        $_SESSION['expired_ticket'] = true;
        unset($_SESSION['expired_ticket']);
        unset($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(["status" => "failed", "message" => "Redirecting to home because ticket is expired."]);
        exit();
    }

    // Set session variables
    $_SESSION['user'] = $user_data['user'];
    $_SESSION['user_id'] = $user_data['user_id'];
    echo json_encode(["status" => "success", "message" => "Logged in automatically.", "user" => $user_data['user']]);

    // Close the statement
    $stmt->close();

    // Close the database connection
    $conn->close();
    
} catch (Exception $e) {
    // Handle any errors
    header('Content-Type: application/json');
    echo json_encode(["status" => "failed", "message" => "Internal Server Error: " . $e->getMessage()]);
    exit();
}
?>
