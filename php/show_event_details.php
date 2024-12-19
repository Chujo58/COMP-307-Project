<?php
session_start();

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

// Database Connection
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $eventID = $_GET['event_id'] ?? '';

    if (empty($eventID)) {
        echo "<p>No event selected.</p>";
        exit();
    }

    function echoLikeCSV($array, $staff, $student)
    {
        echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . ',' . $staff . ',' . $student . ',' . $_SESSION['user_type'] . '\n';
    }

    $query = "SELECT event_name, event_desc, event_start, event_stop, event_filter, event_id, staff_id, student_id FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p>The event selected doesn't exist or has been deleted.</p>";
    } else {
        while ($row = $result->fetch_assoc()) {
            $query_staid = "SELECT f_name, l_name from valid_users WHERE user_id='" . $row['staff_id'] . "'";
            $query_stud = "SELECT f_name, l_name from valid_users WHERE user_id='" . $row['student_id'] . "'";

            $staff = $conn->query($query_staid)->fetch_assoc();
            if ($staff != null) {
                $staff = $staff['f_name'] . ' ' . $staff['l_name'];
            } else {
                $staff = '';
            }

            $student = $conn->query($query_stud)->fetch_assoc();
            if ($student != null) {
                $student = $student['f_name'] . ' ' . $student['l_name'];
            } else {
                $student = '';
            }

            echoLikeCSV($row, $staff, $student);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $start = $_POST['start'] ?? '';
    $stop = $_POST['stop'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $filter = $_POST['filter'] ?? '';
    $type = $_POST['type'] ?? '';
    $eventID = $_POST['event_id'] ?? '';
    $s_id = $_POST['staff_id'] ?? '';

    $query = "SELECT staff_id from events WHERE event_id='" . $eventID . "'";
    $result = $conn->query($query)->fetch_assoc();
    $s_id = $result['staff_id'];

    $id = gen_uuid(10);

    if (empty($name) || empty($start) || empty($stop) || empty($desc) || empty($filter) || empty($type) || empty($s_id)){
        echo "Missing data";
        exit();
    }

    $f_name = $_POST['fname'] ?? '';
    $l_name = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';

    // Check responseText and see if Guest or not and add stuff to form if yes
    if (empty($f_name) || empty($l_name) || $empty($email)){
        if ($_SESSION['user_type'] == 'staff' || $_SESSION['user_type'] == 'student'){
            echo 'Logged user';
        } else {
            echo 'Guest';
            exit();
        }
    }

    //make fname lname and  email part of desc;

    $query = "INSERT INTO `events`(`event_name`,`event_id`,`event_type`,`event_desc`,`event_start`, `event_stop`, `event_filter`,`staff_id`) VALUES ('$name','$id','$type','$desc','$start','$stop','$filter','$s_id')";

    $conn->query($query);
    echo "Created booking";
}

?>