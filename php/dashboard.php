<?php
// Database Connection using SQLite3
$conn = new SQLite3('../comp307project.db');

if (!$conn) {
    die("Connection failed: Unable to connect to SQLite database.");
}

// Handle Loading Course Levels
if (isset($_GET['loadLevels'])) {
    // SQLite3 does not support FLOOR with divisions directly like MySQL. 
    // We can perform the level calculation with a simple approach.
    $query = "SELECT DISTINCT CAST(course_id AS INTEGER) / 100 * 100 AS level FROM course_list ORDER BY level";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->lastErrorMsg());
    }

    // Output each course level as an option
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
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

$query = "SELECT course_name, course_id, course_tag FROM course_list WHERE 1";
$count_query = "SELECT COUNT(*) FROM course_list WHERE 1";

// Apply filters if provided
if (!empty($nameFilter)) {
    $query .= " AND course_name LIKE '%" . $conn->escapeString($nameFilter) . "%'";
    $count_query .= " AND course_name LIKE '%" . $conn->escapeString($nameFilter) . "%'";
}
if (!empty($idFilter)) {
    $query .= " AND course_id LIKE '%" . $conn->escapeString($idFilter) . "%'";
    $count_query .= " AND course_id LIKE '%" . $conn->escapeString($idFilter) . "%'";
}
if (!empty($levelFilter)) {
    // SQLite3 equivalent of FLOOR(course_id / 100) * 100 for level filtering
    $query .= " AND CAST(course_id AS INTEGER) / 100 * 100 = " . $conn->escapeString($levelFilter);
    $count_query .= " AND CAST(course_id AS INTEGER) / 100 * 100 = " . $conn->escapeString($levelFilter);
}

// Execute the query
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->lastErrorMsg());
}

// Output course list as HTML
if ($conn->query($count_query)->fetchArray(SQLITE3_NUM)[0] == 0) {
    echo "<p>No courses found!</p>";
} else {
    $old_index = 0;
    $index = 0;
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        if ($index % 3 == 0 && $index - $old_index == 3) {
            echo "</div>";
            $old_index += 3;
        }
        if ($index % 3 == 0) {
            echo "<div class='box-holder'>";
        }

        // Make the course block clickable with a link to `list_staff.htm`
        echo "<div class='course-block' onclick=\"redirectToStaffList({$row['course_id']})\">";
        echo "<h3>" . htmlspecialchars($row['course_name']) . "</h3>";
        echo "<div class='course_inner'>Course reference: " . '<div class="course_tag">' . htmlspecialchars($row['course_tag']) . '</div>' . '<div class="course_id">' . htmlspecialchars($row['course_id']) . '</div>' . "</div>";
        echo "</div>";

        $index++;
    }
}

$conn->close();
?>
