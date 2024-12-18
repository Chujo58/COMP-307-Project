<?php
    session_start();

    // Check for cookies
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    if (!isset($_COOKIE['ticket_id'])) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "failed", "message" => "Please log in."]);
        exit();
    }
    $ticket_id = $_COOKIE['ticket_id'];

    $stmt = $conn->prepare("SELECT user, user_id, exp_date, user_type FROM valid_users WHERE ticket_id = ?");
    $stmt->bind_param("s", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if ($result->num_rows == 0 || time() > $user_data['exp_date'] || is_null($user_data['exp_date'])) {
        //check if expired
        setcookie('ticket_id', '', time() - 3600, '/');
        $_SESSION['expired_ticket'] = true;
        unset($_SESSION['expired_ticket']);
        unset($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(["status" => "failed", "message" => "Redirecting to home because ticket is expired."]);
        exit();
    }
    $_SESSION['user'] = $user_data['user'];
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['user_type'] = $user_data['user_type'];
    echo json_encode(["status" => "success", "message" => "Logged in automatically.", "user" => $user_data['user']]);
    $stmt->close();

    $conn->close();
?>