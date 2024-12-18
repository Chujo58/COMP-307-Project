<?php

$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Weekly Calendar Data
if (isset($_GET['fetchCalendar'])) {
    $query = "SELECT staff_name, timeslot, booked FROM staff_calendar ORDER BY timeslot";
    $result = $conn->query($query);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    $calendarData = [];
    while ($row = $result->fetch_assoc()) {
        $calendarData[] = $row;
    }
    echo json_encode($calendarData);
    $conn->close();
    exit();
}

// Fetch Upcoming Appointments
if (isset($_GET['fetchAppointments'])) {
    $query = "SELECT course_id, course_name, course_tag, staff_id 
              FROM course_list 
              WHERE staff_id IS NOT NULL 
              ORDER BY course_name";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        echo "<div class='appointment'>";
        echo "<p><strong>Course ID:</strong> " . htmlspecialchars($row['course_id']) . "</p>";
        echo "<p><strong>Course Name:</strong> " . htmlspecialchars($row['course_name']) . "</p>";
        echo "<p><strong>Course Tag:</strong> " . htmlspecialchars($row['course_tag']) . "</p>";
        echo "<p><strong>Staff ID:</strong> " . htmlspecialchars($row['staff_id']) . "</p>";
        echo "</div>";
    }
    $conn->close();
    exit();
}
?>
