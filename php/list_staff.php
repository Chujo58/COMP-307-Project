<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "comp307project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the course ID
$courseID = $_GET['course_id'] ?? '';
if (empty($courseID)) {
    echo "<p>No course selected!</p>";
    exit();
}

// // Prepare the query
// $query = "SELECT staff_id, course_name FROM course_list WHERE course_id = ?";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("s", $courseID);
// $stmt->execute();
// $result = $stmt->get_result();

// // Display staff details
// if ($result->num_rows == 0) {
//     echo "<p>No staff associated with this course!</p>";
// } else {
//     while ($row = $result->fetch_assoc()) {
//         echo "<div class='staff-card'>";
//         echo "<h3>Staff ID: " . htmlspecialchars($row['staff_id']) . "</h3>";
//         echo "<p>Name: " . htmlspecialchars($row['course_name']) . "</p>";
//         echo "</div>";
//     }
// }

// $stmt->close();
// $conn->close();

// Prepare the query to fetch staff details (first_name and last_name) based on staff_id
$query = "
    SELECT vu.l_name, vu.f_name 
    FROM course_list cl 
    INNER JOIN valid_users vu 
    ON cl.staff_id = vu.user_id 
    WHERE cl.course_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $courseID);
$stmt->execute();
$result = $stmt->get_result();

// Display staff details
if ($result->num_rows == 0) {
    echo "<p>No staff associated with this course!</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='staff-card'>";
        echo "<h3>" . htmlspecialchars($row['f_name']) . " " . htmlspecialchars($row['l_name']) . "</h3>";
        echo "</div>";
    }
}

$stmt->close();
$conn->close();
?>