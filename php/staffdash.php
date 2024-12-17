<?php
require_once 'auth.php';

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
    $query = "SELECT appointment_id, staff_name, date, time, location FROM appointments WHERE date >= CURDATE() ORDER BY date, time";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        echo "<div class='appointment'>";
        echo "<p><strong>Staff:</strong> " . htmlspecialchars($row['staff_name']) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($row['date']) . "</p>";
        echo "<p><strong>Time:</strong> " . htmlspecialchars($row['time']) . "</p>";
        echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
        echo "</div>";
    }
    $conn->close();
    exit();
}
?>
