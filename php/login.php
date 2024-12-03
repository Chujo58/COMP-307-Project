<?php 
if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Connect to the SQL database:
    $conn = new mysqli("localhost", "root", "", "comp307project");

    if ($conn->connect_error) {
        die("Internal Server Error: " . $conn->connect_error);
    }

    $sql_validation = "SELECT * FROM valid_users WHERE user='" . $_POST["username"] . "' and pass='" . $_POST["password"] . "'";
    // $sql_availabilites = "SELECT * FROM availabilities WHERE "
    $result = $conn->query($sql_validation);
    if ($result->num_rows > 0){
        $user_id = $conn->query("SELECT user_id FROM valid_users WHERE user='" .  $_POST['username'] . "'")->fetch_assoc()['user_id'];
        $availabilities = $conn->query("SELECT * FROM availabilities WHERE user_id='" . $user_id . "'");
        while ($row = $availabilities->fetch_assoc()){
            echo $row["start_time"] . " to " . $row["stop_time"];
        }
    }
    else {
        echo "Invalid user";
    }
}
?>