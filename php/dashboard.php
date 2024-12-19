<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "comp307project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Loading Course Levels
if (isset($_GET['loadLevels'])) {
    $query = "SELECT DISTINCT FLOOR(course_id / 100) * 100 AS level FROM course_list ORDER BY level";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    // Output each course level as an option
    while ($row = $result->fetch_assoc()) {
        $level = $row['level'];
        echo "<option value='{$level}'>{$level} Level</option>";
    }

    $conn->close();
    exit();
}

// Handle Filtering and Displaying Courses
$nameFilter = $_POST['course-name'] ?? '';
$idFilter = $_POST['course-id'] ?? '';
$levelFilter = $_POST['course-level'] ?? '';

$query = "SELECT DISTINCT course_name, course_id FROM course_list WHERE 1";

// Apply filters if provided
if (!empty($nameFilter)) {
    $query .= " AND course_name LIKE '%" . $conn->real_escape_string($nameFilter) . "%'";
}
if (!empty($idFilter)) {
    $query .= " AND course_id LIKE '%" . $conn->real_escape_string($idFilter) . "%'";
}
if (!empty($levelFilter)) {
    $query .= " AND FLOOR(course_id / 100) * 100 = " . $conn->real_escape_string($levelFilter);
}
// echo($query);

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Output course list as HTML
// if ($result->num_rows == 0) {
//     echo "<p>No courses found!</p>";
// } else {
//     $old_index=0;
//     $index=0;
//     while ($row = $result->fetch_assoc()) {
//         if ($index%3==0 && $index-$old_index==3){
//             echo "</div>";
//             $old_index+=3;
//         }
//         if ($index%3==0){
//             echo "<div class='box-holder'>";
//         }
//         echo "<div class='box course-block'>";
//         echo "<h3 class='heading-higlight'>" . htmlspecialchars($row['course_name']) . "</h3>";
//         echo "<p>Course ID: " . htmlspecialchars($row['course_id']) . "</p>";
//         echo "</div>";
//         $index ++;
//     }
// }
if ($result->num_rows == 0) {
    echo "<p>No courses found!</p>";
} else {
    $old_index = 0;
    $index = 0;
    while ($row = $result->fetch_assoc()) {
        if ($index % 3 == 0 && $index - $old_index == 3) {
            echo "</div>";
            $old_index += 3;
        }
        if ($index % 3 == 0) {
            echo "<div class='box-holder'>";
        }

        // Make the course block clickable with a link to `list_staff.htm`
        echo "<div class='box course-block' onclick=\"redirectToStaffList({$row['course_id']})\">";
        echo "<h3 class='heading-higlight'>" . htmlspecialchars($row['course_name']) . "</h3>";
        echo "<p>Course ID: " . htmlspecialchars($row['course_id']) . "</p>";
        echo "</div>";

        $index++;
    }
}

$conn->close();
?>
