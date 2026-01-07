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
    $total = $_POST['total'];
    $class_id = $_POST['class_id'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO quiz (title, total, date, class_id) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sii", $title, $total, $class_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "New exam added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid Request";
}
?>
