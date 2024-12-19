<?php 
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

session_start();

function display($path){
    $file = fopen($path,"r");
    while (!feof($file)) {
        $line = fgets($file);
        echo $line;
    }
    fclose($file);
}

display("matter/top.htm");

if (sizeof($_GET) == 0) {
    display("matter/main.htm");
}
else {
    switch ($_GET["Page"]) {
        case "Home": 
            display("matter/main.htm");
            break;
        case "Dashboard":
            if ($_SESSION['user_type'] == 'student'){
                echo "<script>window.addEventListener('load', loadLevels); window.addEventListener('load', filterCourses);</script>";
                display("matter/dashboard.htm");
            }
            elseif ($_SESSION['user_type'] == 'staff'){
                display("matter/staffdash.htm");
                echo "<script>window.addEventListener('load', fetchApt);</script>";
                echo "<script>window.addEventListener('load', onLoad);</script>";
            }
            else {
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
        case "AddCourse":
            if ($_SESSION['user_type'] == 'staff'){
                display("matter/addcourse.htm");
            }
            else {
                echo "Invalid user type";
            }
            break;
        case "Calendar":
            $userID = isset($_GET['user_id']) ? $_GET["user_id"] : null;
            $use_session = isset($_GET['session']) ? $_GET['session'] : null;
            if ($use_session){
                $userID = $_SESSION['user_id'];
            }
            if ($userID){
                echo "<script>const userID ='" . $userID . "';</script>";
            }
            echo "<script>window.addEventListener('load', onLoad);</script>";
            display('matter/calendar.htm');
            break;

        case "Event":
            $eventID = isset($_GET['event_id']) ? $_GET["event_id"] : null;
            if ($eventID){
                echo "<script>const eventID ='" . $eventID . "'; window.addEventListener('load', popoutEvent);</script>";
                echo "<script>window.addEventListener('load', onLoad); window.addEventListener('load', forceMobile);</script>";
            }
            display('matter/event.htm');
            break;

        default:
            echo "<p>Page not found!</p>";
            break;
    }
}

display("matter/bot.htm");

?>