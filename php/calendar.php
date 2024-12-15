<?php
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error){
    die("Internal Server Error: " . $conn->connect_error);
}

function echoLikeCSV($array){
    echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . '\n';
}

function showData($method, $conn){
    $start = $method['start'];
    $stop = $method['stop'];

    $result = $conn->query("SELECT * FROM events WHERE event_start BETWEEN '" . $start ."' AND '" . $stop . "'");

    if ($result->num_rows == 0){
        echo "No events";
    }
    else {
        while ($row = $result->fetch_assoc()){
            echoLikeCSV($row);
            // echo $row['event_name'];
            // echo '<br>';
            // echo $row['event_desc'];
            // echo '<br>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    // $time_stamp = $_GET['date'];
    showData($_GET,$conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    showData($_POST,$conn);
}
?>