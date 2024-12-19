<?php
    // session_start();

    // function authenticate($conn) {
    //     if (!isset($_COOKIE['ticket_id'])) {
    //         $_SESSION['user_type'] = "guest";
    //         return ["status" => "failed", "message" => "No ticket found. Please log in."];
    //     }
    
    //     $ticket_id = $_COOKIE['ticket_id'];
    
    //     $stmt = $conn->prepare("SELECT user, user_id, exp_date, user_type FROM valid_users WHERE ticket_id = ?");
    //     $stmt->bind_param("s", $ticket_id);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $user_data = $result->fetch_assoc();
    
    //     if ($result->num_rows == 0 || time() > $user_data['exp_date'] || is_null($user_data['exp_date'])) {
    //         // Expired or invalid ticket
    //         setcookie('ticket_id', '', time() - 3600, '/'); // Clear the cookie
    //         unset($_SESSION['user'], $_SESSION['user_id']);
    //         $_SESSION['user_type'] = "guest";
    //         return ["status" => "failed", "message" => "Session expired. Please log in again."];
    //     }
    
    //     // Valid session
    //     $_SESSION['user'] = $user_data['user'];
    //     $_SESSION['user_id'] = $user_data['user_id'];
    //     $_SESSION['user_type'] = $user_data['user_type'];
    
    //     return [
    //         "status" => "success",
    //         "message" => "Authenticated successfully.",
    //         "user" => $user_data['user'],
    //         "user_type" => $user_data['user_type']
    //     ];
    // }
    
    // header('Content-Type: application/json');
    
    // // Database connection
    // $conn = new mysqli("localhost", "root", "", "comp307project");
    // if ($conn->connect_error) {
    //     echo json_encode(["status" => "error", "message" => "Internal Server Error."]);
    //     exit();
    // }
    
    // // Authenticate the user
    // $response = authenticate($conn);
    // echo json_encode($response);
    
    // $conn->close();



session_start();

function authenticate($conn) {
    if (!isset($_COOKIE['ticket_id'])) {
        $_SESSION['user_type'] = "guest";
        return ["status" => "failed", "message" => "No ticket found. Please log in."];
    }

    $ticket_id = $_COOKIE['ticket_id'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT user, user_id, exp_date, user_type FROM valid_users WHERE ticket_id = :ticket_id");
    $stmt->bindValue(':ticket_id', $ticket_id, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user_data = $result->fetchArray(SQLITE3_ASSOC);

    // Check if the ticket is valid or expired
    if (!$user_data || time() > $user_data['exp_date'] || is_null($user_data['exp_date'])) {
        // Expired or invalid ticket
        setcookie('ticket_id', '', time() - 3600, '/'); // Clear the cookie
        unset($_SESSION['user'], $_SESSION['user_id']);
        $_SESSION['user_type'] = "guest";
        return ["status" => "failed", "message" => "Session expired. Please log in again."];
    }

    // Valid session
    $_SESSION['user'] = $user_data['user'];
    $_SESSION['user_id'] = $user_data['user_id'];
    $_SESSION['user_type'] = $user_data['user_type'];

    return [
        "status" => "success",
        "message" => "Authenticated successfully.",
        "user" => $user_data['user'],
        "user_type" => $user_data['user_type']
    ];
}

header('Content-Type: application/json');

// Database connection
try {
    $conn = new SQLite3('../comp307project.db');
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Internal Server Error: Unable to connect to the database."]);
    exit();
}

// Authenticate the user
$response = authenticate($conn);
echo json_encode($response);

$conn->close();
?>
