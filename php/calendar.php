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


$conn = new SQLite3('../comp307project.db');
if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

function echoLikeCSV($array){
    echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . '\n';
}

function showData($result) {
    if (!$result) {
        echo "No events";
        return;
    }

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echoLikeCSV($row);
    }
}

if (isset($_GET['get_name_id'])){
    $id = $_GET['get_name_id'];

    $stmt = $conn->prepare("SELECT f_name, l_name FROM valid_users WHERE user_id = :id");
    $count = $conn->prepare("SELECT COUNT(f_name) AS count FROM valid_users WHERE user_id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_TEXT);
    $count->bindValue(':id', $id, SQLITE3_TEXT);
    $result = $stmt->execute();
    $count = $count->execute();
    $numRows = $count->fetchArray(SQLITE3_ASSOC)['count'];

    if ($numRows == 0){
        echo "";
        exit();
    }
    
    $result = $result->fetchArray(SQLITE3_ASSOC);
    echo $result['f_name'] . ',' . $result['l_name'] . '\n';
    exit();
}

if (isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = :event_id");
    $stmt->bindValue(':event_id', $_GET['event_id'], SQLITE3_TEXT);
    $stmt->execute();
    echo "Deleted event";
}

if (isset($_GET['loadFilters'])) {
    $user = $_GET['user'] ?? '';
    $query = "SELECT DISTINCT event_filter FROM events";
    if (!empty($user)) {
        $query .= " WHERE (staff_id = :user OR student_id = :user)";
    }
    $query .= " ORDER BY event_filter";

    $stmt = $conn->prepare($query);
    if (!empty($user)) {
        $stmt->bindValue(':user', $user, SQLITE3_TEXT);
    }
    $result = $stmt->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<div class="filter"><input type="checkbox" checked="true" event_filter="' . $row['event_filter'] . '" onclick="changeFilter();"><span>' . $row['event_filter'] . '</span></div>';
    }

    $conn->close();
    exit();
}


if (isset($_GET['start']) && isset($_GET['stop'])){
    $start = $_GET['start'];
    $stop = $_GET['stop'];
    $query = "SELECT * FROM events WHERE event_start BETWEEN :start AND :stop";

    $filter = $_GET['filter'] ?? '';
    $user = $_GET['user'] ?? '';
    $type = $_GET['type'] ?? '';
    
    if (!empty($filter)) {
        $query .= " AND event_filter = :filter";
    }
    if (!empty($user)) {
        $query .= " AND (staff_id = :user OR student_id = :user)";
    }
    if (!empty($type)) {
        $query .= " AND event_type = :type";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':start', $start, SQLITE3_TEXT);
    $stmt->bindValue(':stop', $stop, SQLITE3_TEXT);
    if (!empty($filter)) {
        $stmt->bindValue(':filter', $filter, SQLITE3_TEXT);
    }
    if (!empty($user)) {
        $stmt->bindValue(':user', $user, SQLITE3_TEXT);
    }
    if (!empty($type)) {
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
    }

    showData($stmt->execute());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    session_start();
    
    if (!isset($_SESSION['user_type'])){
        echo "Guest";
    }

    if ($_SESSION['user_type'] == 'staff'){
        $name = $_POST['name'] ?? '';
        $start = $_POST['start'] ?? '';
        $stop = $_POST['stop'] ?? '';
        $desc = $_POST['desc'] ?? '';
        $filter = $_POST['filter'] ?? '';

        $id = gen_uuid(10);
        $type = 'availability';
        $s_id = $_SESSION['user_id'];

        if (empty($name) || empty($start) || empty($stop) || empty($desc) || empty($filter)){
            echo "Cannot add empty event";
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO events (event_name, event_id, event_recurrance, event_type, event_desc, event_start, event_stop, event_filter, staff_id, student_id) VALUES (:name, :id, '', :type, :desc, :start, :stop, :filter, :staff_id, '')");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
        $stmt->bindValue(':desc', $desc, SQLITE3_TEXT);
        $stmt->bindValue(':start', $start, SQLITE3_TEXT);
        $stmt->bindValue(':stop', $stop, SQLITE3_TEXT);
        $stmt->bindValue(':filter', $filter, SQLITE3_TEXT);
        $stmt->bindValue(':staff_id', $s_id, SQLITE3_TEXT);

        $stmt->execute();
        echo "Created event";
    }
    
    //For staff:
    //Edit event in php get show_event_details.php to show edit form
    
    //only show availabilities on calendar for staff

    //For student:
    //Show booking form with get show_event_details.php
    //auto complete all aside start, stop

}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    session_start();
    if ($_SESSION['user_type'] == 'student'){
        echo "Student";
    } elseif ($_SESSION['user_type'] == 'staff') {
        echo "Staff";
    } else {
        echo "Guest";
    }
}

?>