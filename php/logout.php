<?php
    session_start();

    if (isset($_COOKIE['ticket_id'])) {
        $token = $_COOKIE['ticket_id'];

        $conn = new mysqli("localhost", "root", "", "comp307project");

        if ($conn->connect_error) {
            die("Internal Server Error: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE valid_users SET ticket_id = NULL, exp_date = NULL WHERE ticket_id = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();

        setcookie('ticket_id', "", time() - 3600, "/", "", true, true);

        $conn->close();
    }
    unset($_SESSION['expired_ticket']);
    unset($_SESSION['user_id']);

    session_unset();
    session_destroy();

    // header("Location: ../index.php?Page=Home&reload=true");
    exit();
?>