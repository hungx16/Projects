<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db"; // Update with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $title = $_POST['title'];

    $class_id = $_POST['class_id'];
    $hours = isset($_POST['exam_hours']) ? (int) $_POST['exam_hours'] : 0;
    $minutes = isset($_POST['exam_minutes']) ? (int) $_POST['exam_minutes'] : 0;
    $exam_time = ($hours * 60 + $minutes) * 60; // convert to seconds

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO quiz (title, date, class_id, exam_time) VALUES (?, NOW(), ?, ?)");
    $stmt->bind_param("sii", $title, $class_id, $exam_time);

    // Execute the query
    if ($stmt->execute()) {
        echo "New exam added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else if (isset($_GET['eid'])) {
    $eid = intval($_GET['eid']);
    $query = "DELETE FROM quiz WHERE eid = $eid";

    if (mysqli_query($conn, $query)) {
        echo ("Exam deleted successfully.");
    } else {
        echo "Error deleting exam: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}

?>