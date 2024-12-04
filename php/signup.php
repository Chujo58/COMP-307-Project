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

    //Verify is user exists
    $verify_user = "SELECT user FROM valid_users WHERE user='" . $_POST["username"] . "'";
    if ($conn->query($verify_user)->num_rows > 0){
        echo "User already exists";
        exit();
    }

    //If user does not exist, check that the password is equal in the two form fields:
    if ($_POST["password"] != $_POST["confirm_password"]){
        echo "Passwords not matching";
        exit();
    }

    $user_id = gen_uuid(10);

    //Validate the sign up
    $insert_user = "INSERT INTO `valid_users` (`user`, `pass`, `user_id`) VALUES ('" . $_POST['username'] . "','" . $_POST["password"] . "','" . $user_id . "')";
    $conn->query($insert_user);
    echo "User added";
}

?>