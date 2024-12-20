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
$conn = new SQLite3('../comp307project.db');
if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $eventID = $_GET['event_id'] ?? '';

    if (empty($eventID)) {
        echo "<p>No event selected.</p>";
        exit();
    }

    function echoLikeCSV($array, $staff, $student)
    {
        echo $array['event_name'] . ',' . $array['event_desc'] . ',' . $array['event_start'] . ',' . $array['event_stop'] . ',' . $array['event_filter'] . ',' . $array['event_id'] . ',' . $staff . ',' . $student . ',' . $_SESSION['user_type'] . ',' . $array['event_type'] . '\n';
    }

    $query = "SELECT event_name, event_desc, event_start, event_stop, event_filter, event_id, staff_id, student_id, event_type FROM events WHERE event_id = :event_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':event_id', $eventID, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($result->numColumns() == 0) {
        echo "<p>The event selected doesn't exist or has been deleted.</p>";
    } else {
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $query_staid = "SELECT f_name, l_name from valid_users WHERE user_id=:staff_id";
            $query_stud = "SELECT f_name, l_name from valid_users WHERE user_id=:student_id";

            $stmt_staid = $conn->prepare($query_staid);
            $stmt_staid->bindValue(':staff_id', $row['staff_id'], SQLITE3_TEXT);
            $staff_result = $stmt_staid->execute()->fetchArray(SQLITE3_ASSOC);

            if ($staff_result != null) {
                $staff = $staff_result['f_name'] . ' ' . $staff_result['l_name'];
            } else {
                $staff = '';
            }

            $stmt_stud = $conn->prepare($query_stud);
            $stmt_stud->bindValue(':student_id', $row['student_id'], SQLITE3_TEXT);
            $student_result = $stmt_stud->execute()->fetchArray(SQLITE3_ASSOC);

            if ($student_result != null) {
                $student = $student_result['f_name'] . ' ' . $student_result['l_name'];
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

    $query = "SELECT staff_id from events WHERE event_id=:event_id";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':event_id', $eventID, SQLITE3_TEXT);
    $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
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
    if (empty($f_name) || empty($l_name) || empty($email)){
        if (!($_SESSION['user_type'] == 'staff' || $_SESSION['user_type'] == 'student')){
            echo 'Guest';
            exit();
        }
    }

    if ($_SESSION['user_type'] == 'staff' || $_SESSION['user_type'] == 'student'){
        $other_id = $_SESSION['user_id'];
    } else {
        $other_id = gen_uuid(10);
        $stmt = $conn->prepare("INSERT INTO valid_users (user, f_name, l_name, user_type, user_id) VALUES (:email, :f_name, :l_name, 'guest', :user_id)");
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':f_name', $f_name, SQLITE3_TEXT);
        $stmt->bindValue(':l_name', $l_name, SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $other_id, SQLITE3_TEXT);
        $stmt->execute();
    }

    $stmt = $conn->prepare("INSERT INTO events (event_name, event_id, event_type, event_desc, event_start, event_stop, event_filter, staff_id, student_id) VALUES (:name, :event_id, :event_type, :event_desc, :event_start, :event_stop, :event_filter, :staff_id, :student_id)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':event_id', $id, SQLITE3_TEXT);
    $stmt->bindValue(':event_type', $type, SQLITE3_TEXT);
    $stmt->bindValue(':event_desc', $desc, SQLITE3_TEXT);
    $stmt->bindValue(':event_start', $start, SQLITE3_TEXT);
    $stmt->bindValue(':event_stop', $stop, SQLITE3_TEXT);
    $stmt->bindValue(':event_filter', $filter, SQLITE3_TEXT);
    $stmt->bindValue(':staff_id', $s_id, SQLITE3_TEXT);
    $stmt->bindValue(':student_id', $other_id, SQLITE3_TEXT);
    $stmt->execute();

    echo "Created booking";
}
?>