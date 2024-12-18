<?php
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error){
    die("Internal Server Error: " . $conn->connect_error);
}

function echoLikeCSV($array){
    echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . '\n';
}

function showData($query, $conn){
    $result = $conn->query($query);

    if ($result->num_rows == 0){
        echo "No events";
    }
    else {
        while ($row = $result->fetch_assoc()){
            echoLikeCSV($row);
        }
    }
}

if (isset($_GET['loadFilters'])) {
    $query = "SELECT DISTINCT event_filter FROM events ORDER BY event_filter";
    $result = $conn->query($query);

    if (!$result){
        die("Query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()){
        echo '<div class="filter"><input type="checkbox" checked="true" event_filter="' . $row['event_filter'] . '" onclick="changeFilter();"><span>' . $row['event_filter'] . '</span></div>';
    }

    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $start = $_POST['start'];
    $stop = $_POST['stop'];

    $query = "SELECT * FROM events WHERE event_start BETWEEN '" . $start ."' AND '" . $stop . "'";

    $filter = $_POST['filter'] ?? '';
    $user = $_POST['user'] ?? '';
    
    if (!empty($filter)){
        $query .= " AND event_filter='" . $filter . "'";
    }
    if (!empty($user)){
        $query .= " AND (staff_id='" . $user . "' OR student_id='" . $user . "')";   
    }

    
    showData($query, $conn);
}
?>