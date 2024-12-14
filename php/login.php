<?php 
//Id generating from here: https://stackoverflow.com/questions/307486/short-unique-id-in-php
function gen_uuid($len=8) {

    $hex = md5("yourSaltHere" . uniqid("", true));

    $pack = pack('H*', $hex);
    $tmp =  base64_encode($pack);

    $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);

    $len = max(4, min(128, $len));

    while (strlen($uid) < $len)
        $uid .= gen_uuid(22);

    return substr($uid, 0, $len);
}

function generateTicket($conn, $user_id, $exp_date){
    $ticket_id = gen_uuid(30);
    $conn->query("UPDATE valid_users SET ticket_id='" . $ticket_id . "', exp_date='" . $exp_date . "' WHERE user_id='" . $user_id . "'"); 
    

    return $ticket_id;
}

// Check for cookies
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    die("Internal Server Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!isset($_COOKIE['ticket_id'])) {
        echo "Require Log In or Expired Ticket";
        exit();
    }
    $ticket_id = $_COOKIE['ticket_id'];

    $stmt = $conn->prepare("SELECT user, user_id, exp_date FROM valid_users WHERE ticket_id = ?");
    $stmt->bind_param("s", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if ($result->num_rows > 0) {
        //check if expired
        if (time() > $user_data['exp_date'] || is_null($user_data['exp_date'])) {
            $_SESSION['expired_ticket'] = true;
            setcookie('ticket_id', '', time() - 3600, '/');
            echo "Expired ticket.";
            exit();
        } else {
            echo $user_data['user'];
        }
    } else {
        echo "Require Log In or Expired Ticket";
        exit();
    }
    $stmt->close();
}


if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Connect to the SQL database
    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    // Sanitize input
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verify if user exists and get hashed password + user_id
    $stmt = $conn->prepare("SELECT user, pass, user_id, ticket_id, exp_date FROM valid_users WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Invalid user";
        exit();
    }

    $user_data = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user_data["pass"])) {
        echo "Invalid password";
        exit();
    }

    // Check if the user already has a ticket
    $cookie_expiry = time() + (2 * 24 * 60 * 60); // 2 days (60 * 60 min = 1 hour then mult 24 then 2)

    if (is_null($user_data["ticket_id"]) || time() > $user_data["exp_date"]) {
        $ticket_id = generateTicket($conn, $user_data["user_id"], $cookie_expiry);
    } else {
        $ticket_id = $user_data["ticket_id"];
    }

    // Set cookies
    setcookie("ticket_id", $ticket_id, $cookie_expiry, "/", "", true, true);
    $_SESSION['expired_ticket'] = false;

    $stmt->close();
}
$conn->close();
?>