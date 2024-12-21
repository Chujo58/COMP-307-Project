<?php
// Ling Jien Chen 
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
    $conn = new SQLite3('../comp307project.db');
    if (!$conn){
        die("Internal Server Error");
    }

    // Retrieve input values and sanitize
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords Must Match";
        exit();
    }

    // Validate email
    $email = explode('@', $username);
    if (count($email) != 2 || ($email[1] != "mail.mcgill.ca" && $email[1] != "mcgill.ca")) {
        echo "Please Use a Valid McGill Email as Username";
        exit();
    }

    //Verify is user exists
    $verify_user = $conn->prepare("SELECT COUNT(*) AS count FROM valid_users WHERE user = :user");
    $verify_user->bindParam(":user", $username);
    $verify_user = $verify_user->execute();

    $numAccs = $verify_user->fetchArray(SQLITE3_ASSOC)['count'];
    if ($numAccs > 0) {
        echo "User Already Exists.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_id = gen_uuid(10);

    // Determine user type
    $user_type = "student";
    if ($email[1] == "mcgill.ca") {
        $user_type = "staff";
    }
    //Validate the sign up
    $insert_user = $conn->prepare("INSERT INTO valid_users (user, pass, user_id, user_type, f_name, l_name) VALUES (:user, :pass, :id, :user_type, :f_name, :l_name)");
    $insert_user->bindParam(':user',$username, SQLITE3_TEXT);
    $insert_user->bindParam(':pass',$hashed_password, SQLITE3_TEXT);
    $insert_user->bindParam(':id',$user_id, SQLITE3_TEXT);
    $insert_user->bindParam(':user_type',$user_type, SQLITE3_TEXT);
    $insert_user->bindParam(':f_name',$f_name, SQLITE3_TEXT);
    $insert_user->bindParam(':l_name',$l_name, SQLITE3_TEXT);


    if ($insert_user->execute()) {
        echo "User Added";
    } else {
        echo "Failed" . $conn->lastErrorMsg();
    }

    $insert_user->close();
    $conn->close();
}

?>