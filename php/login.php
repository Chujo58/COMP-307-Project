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

function generateTicket($conn, $user_id){
    $ticket_id = gen_uuid(30);
    $conn->query("UPDATE valid_users SET ticket_id='" . $ticket_id . "' WHERE user_id='" . $user_id . "'");   
    $conn->query("INSERT INTO `tickets` (`user_id`, `ticket_id`, `exp_date`) VALUE ('" . $user_id . "','" . $ticket_id . "','"); //ADD 2 days to current time;
    return $ticket_id;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Connect to the SQL database:
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    //Verify if user is valid
    $verify_user = "SELECT user FROM valid_users WHERE user='" . $_POST["username"] . "'";
    if ($conn->query($verify_user)->num_rows == 0){
        echo "Invalid user";
        exit();
    }
    
    //If user is valid verify password
    $verify_pass = "SELECT pass FROM valid_users WHERE user='" . $_POST["username"] . "'";
    if ($conn->query($verify_pass)->fetch_assoc()["pass"] != $_POST["password"]) {
        echo "Invalid password";
        exit();
    }

    //Validate the login
    // echo "User logged in";
    $query_ticket = "SELECT ticket_id FROM valid_users WHERE user='" . $_POST["username"] . "' AND pass='" . $_POST["password"] . "'";
    $ticket_res = $conn->query($query_ticket);
    if ($ticket_res->num_rows == 0){
        print("Not valid");
    }
    else if (empty($ticket_res->fetch_assoc()["ticket_id"])){
        $user_id = $conn->query("SELECT user_id FROM valid_users WHERE user='" . $_POST["username"] . "' AND pass='" . $_POST["password"] . "'");
        print($ticket_id);
    }
    else {
        print($ticket_res->fetch_assoc()["ticket_id"]);
    }
}
?>