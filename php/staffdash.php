<?php

$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Weekly Calendar Data
if (isset($_GET['fetchCalendar'])) {
    header("Content-Type: application/json");
    $query = $conn->prepare("SELECT staff_name, timeslot, booked FROM staff_calendar ORDER BY timeslot");
    $query->execute();
    $result = $query->get_result();

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
        exit();
    }

    $calendarData = [];
    while ($row = $result->fetch_assoc()) {
        $calendarData[] = $row;
    }
    echo json_encode($calendarData);
    $query->close();
    exit();
}

// Fetch Upcoming Appointments
if (isset($_GET['fetchAppointments'])) {
    // Set the 'staff_id' dynamically (e.g., from session or request).
    $current_staff_id = $_GET['staff_id'] ?? null; // Example for getting the staff_id dynamically

    if (!$current_staff_id) {
        http_response_code(400);
        echo "Error: Staff ID is required.";
        exit();
    }

    // Fetch events where staff_id matches the current staff_id
    $query = $conn->prepare("SELECT event_desc, event_name, event_start, event_stop, student_id 
                             FROM events 
                             WHERE staff_id = ?
                             ORDER BY event_start");
    $query->bind_param("s", $current_staff_id); // Bind the staff_id parameter to the query
    $query->execute();
    $result = $query->get_result();

    if (!$result) {
        http_response_code(500);
        echo "Error fetching appointments.";
        exit();
    }

    // Output the events as formatted HTML
    while ($row = $result->fetch_assoc()) {
        echo "<div class='appointment'>";
        echo "<p><strong>Event Name:</strong> " . htmlspecialchars($row['event_name']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['event_desc']) . "</p>";
        echo "<p><strong>Start Time:</strong> " . htmlspecialchars($row['event_start']) . "</p>";
        echo "<p><strong>End Time:</strong> " . htmlspecialchars($row['event_stop']) . "</p>";
        echo "<p><strong>Student ID:</strong> " . htmlspecialchars($row['student_id']) . "</p>";
        echo "</div>";
    }

    $query->close();
    exit();
}

// Close connection
register_shutdown_function(function () use ($conn) {
    if ($conn) {
        $conn->close();
    }
});
?>
