<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

session_start();

function display($path)
{
    $file = fopen($path, "r");
    while (!feof($file)) {
        $line = fgets($file);
        echo $line;
    }
    fclose($file);
}

display("matter/top.htm");

$conn = new SQLite3('comp307project.db');
if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

if (sizeof($_GET) == 0) {
    display("matter/main.htm");
} else {
    switch ($_GET["Page"]) {
        case "Home":
            display("matter/main.htm");
            break;
        case "Dashboard":
            if ($_SESSION['user_type'] == 'student') {
                echo "<script>window.addEventListener('load', loadLevels); window.addEventListener('load', filterCourses);</script>";
                display("matter/dashboard.htm");
            } elseif ($_SESSION['user_type'] == 'staff') {
                display("matter/staffdash.htm");
                echo "<script>const userID ='" . $_SESSION['user_id'] . "';</script>";
                echo "<script>window.addEventListener('load', fetchApt);</script>";
                echo "<script>window.addEventListener('load', onLoad);</script>";
            } else {
                echo "Invalid user type";
            }
            break;
        case "StaffList":
            $courseID = isset($_GET["course_id"]) ? $_GET["course_id"] : null; //check course_id provided
            if ($courseID) {
                // Pass the course_id as a JavaScript variable for use in the staff list
                echo "<script>const courseID = '$courseID'; window.addEventListener('load', loadStaff);</script>";
                display("matter/list_staff.htm");
            } else {
                echo "<p>No course selected!</p>";
            }
            break;
        case "StaffCourses":
            if ($_SESSION['user_type'] == 'staff') {
                display("matter/staffcourses.htm");
                echo "<script>window.addEventListener('load', fetchCourses);</script>";
            } else {
                echo "Invalid user type";
            }
            break;
        case "AddCourse":
            if ($_SESSION['user_type'] == 'staff') {
                display("matter/addcourse.htm");
            } else {
                echo "Invalid user type";
            }
            break;

        case "Calendar":
            $userID = isset($_GET['user_id']) ? $_GET["user_id"] : null;
            $use_session = isset($_GET['session']) ? $_GET['session'] : null;
            if ($use_session && !$userID) {
                $userID = $_SESSION['user_id'];
            }
            if ($userID) {
                echo "<script>const userID ='" . $userID . "';</script>";
            } else {
                echo "<script>const eventType='availability';</script>";
            }

            $stmt = $conn->prepare("SELECT user_type from valid_users where user_id= :id");
            $stmt->bindValue(':id',$userID);
            $user_type = $stmt->execute()->fetchArray(SQLITE3_ASSOC)['user_type'];
            
            if ($user_ID && $_SESSION["user_type"] == "student" && $user_type == 'staff') {
                echo "<script>const eventType='availability';</script>";
            } else {
                echo "";
            }
            
            echo "<script>window.addEventListener('load', onLoad); window.addEventListener('load', function(){ getUserNames('$userID'); });</script>";
            display('matter/calendar.htm');
            break;

        case "Event":
            $eventID = isset($_GET['event_id']) ? $_GET["event_id"] : null;
            if ($eventID) {
                //Go get the event filter:
                
                $stmt = $conn->prepare("SELECT event_filter from events WHERE event_id = :id");
                $stmt->bindValue(':id', $eventID, SQLITE3_TEXT);
                $eventFilter = $stmt->execute()->fetchArray(SQLITE3_ASSOC)['event_filter'];

                //Filter calendar:
                echo "<script>window.addEventListener('load', onLoad);</script>";
                echo "<script>const eventID ='$eventID'; const eventType='booking'; const eventFilter='$eventFilter'; window.addEventListener('load', popoutEvent);</script>";
            }
            display('matter/event.htm');
            break;

        case "PendingRequests":
            if ($_SESSION['user_type'] == 'staff') {
                // Add the Pending Requests page and load the corresponding JS function
                echo "<script>window.addEventListener('load', loadPendingRequests);</script>";
                display("matter/pending.htm");
            } else {
                echo "<p>Unauthorized access!</p>";
            }
            break;

        default:
            echo "<p>Page not found!</p>";
            break;
    }
}

display("matter/bot.htm");
$conn->close();
?>