<?php
session_start();

if (isset($_COOKIE['ticket_id'])) {
    $token = $_COOKIE['ticket_id'];

    // Connect to SQLite3 database
    $db = new SQLite3('../comp307project.db');

    // Prepare and execute the UPDATE query
    $stmt = $db->prepare("UPDATE valid_users SET ticket_id = NULL, exp_date = NULL WHERE ticket_id = :token");
    $stmt->bindValue(':token', $token, SQLITE3_TEXT);
    $stmt->execute();

    // Clean up and close the statement and database connection
    $stmt->close();
    $db->close();

    // Expire the 'ticket_id' cookie
    setcookie('ticket_id', "", time() - 3600, "/", "", true, true);
}

// Unset session variables
unset($_SESSION['expired_ticket']);
unset($_SESSION['user_id']);
    unset($_SESSION['user_type']);

session_unset();
session_destroy();

// Optionally redirect to a page
// header("Location: ../index.php?Page=Home&reload=true");
exit();
?>
