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

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    //Connect to the SQL database:
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //Check if it is the same
    if ($password !== $confirm_password) {
        echo "Passwords do not match";
        exit();
    }

    //Verify is user exists
    $verify_user = $conn->prepare("SELECT user FROM valid_users WHERE user = ?");
    $verify_user->bind_param("s", $username);
    $verify_user->execute();
    $verify_user->store_result();
    if ($verify_user->num_rows > 0) {
        echo "User already exists.";
        $verify_user->close();
        exit();
    }
    $verify_user->close();

    //Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $user_id = gen_uuid(10);

    //Validate the sign up
    $insert_user = $conn->prepare("INSERT INTO valid_users (user, pass, user_id) VALUES (?, ?, ?)");
    $insert_user->bind_param("sss", $username, $hashed_password, $user_id);
    if ($insert_user->execute()) {
        echo "User added";
    } else {
        echo "Failed";
    }
    $insert_user->close();

    $conn->close();
}

?>