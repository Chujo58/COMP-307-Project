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

if (isset($_GET['delete'])){
    $query = "DELETE FROM events WHERE event_id='" . $_GET["event_id"] . "'";
    $conn->query($query);
    echo "Deleted event";
}

if (isset($_GET['loadFilters'])) {
    $user = $_GET['user'] ?? '';
    $query = "SELECT DISTINCT event_filter FROM events";
    if (!empty($user)) {
        $query .= " WHERE (staff_id='" . $user . "' OR student_id='" . $user . "')";
    }
    $query .= " ORDER BY event_filter";

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


if (isset($_GET['start']) && isset($_GET['stop'])){
    $start = $_GET['start'];
    $stop = $_GET['stop'];
    $query = "SELECT * FROM events WHERE event_start BETWEEN '" . $start ."' AND '" . $stop . "'";

    $filter = $_GET['filter'] ?? '';
    $user = $_GET['user'] ?? '';
    $type = $_GET['type'] ?? '';
    
    if (!empty($filter)){
        $query .= " AND event_filter='" . $filter . "'";
    }
    if (!empty($user)){
        $query .= " AND (staff_id='" . $user . "' OR student_id='" . $user . "')";   
    }
    if (!empty($type)){
        $query .= " AND event_type='" . $type . "'";
    }

    showData($query, $conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    session_start();

    if ($_SESSION['user_type'] == 'staff'){
        $name = $_POST['name'];
        $start = $_POST['start'];
        $stop = $_POST['stop'];
        $desc = $_POST['desc'];
        $filter = $_POST['filter'];

        $id = gen_uuid(10);
        $type = 'availability';
        $s_id = $_SESSION['user_id'];

        $query = "INSERT INTO `events`(`event_name`, `event_id`, `event_recurrance`, `event_type`, `event_desc`, `event_start`, `event_stop`, `event_filter`, `staff_id`, `student_id`) VALUES ('$name','$id','','$type','$desc','$start','$stop','$filter','$s_id','')";

        $conn->query($query);
        echo "Created event";
    }
    if ($_SESSION['user_type'] == 'student'){
        echo "Student";
    }
    //For staff:
    //Edit event in php get show_event_details.php to show edit form
    
    //only show availabilities on calendar for staff

    //For student:
    //Show booking form with get show_event_details.php
    //auto complete all aside start, stop

}

?>