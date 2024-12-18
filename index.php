<?php 
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

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
            display("matter/dashboard.htm");
            break;
        case "StaffList":
            $courseID = isset($_GET["course_id"]) ? $_GET["course_id"] : null; //check course_id provided
            if ($courseID) {
                // Pass the course_id as a JavaScript variable for use in the staff list
                echo "<script>const courseID = '$courseID';</script>";
                display("matter/list_staff.htm");
            } else {
                echo "<p>No course selected!</p>";
            }
            break;
        case "Calendar":
            display('matter/calendar.htm');
            break;

        case "StaffDash":
            display("matter/staffdash.htm");
            break;
        default:
            echo "<p>Page not found!</p>";
            break;
    }
}

display("matter/bot.htm");

?>