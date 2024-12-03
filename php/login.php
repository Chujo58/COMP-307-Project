<?php 
if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Connect to the SQL database:
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    // echo $_POST["username"] . " " . $_POST["password"] ."";

    $verify_user = "SELECT user FROM valid_users WHERE user='" . $_POST["username"] . "'";
    if ($conn->query($verify_user)->num_rows == 0){
        echo "Invalid user";
        exit();
    }
    
    $verify_pass = "SELECT pass FROM valid_users WHERE user='" . $_POST["username"] . "'";
    if ($conn->query($verify_pass)->fetch_assoc()["pass"] != $_POST["password"]) {
        echo "Invalid password";
        exit();
    }
    
}
?>