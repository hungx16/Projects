<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db"; // Replace with your actual database name

// Set response header
header('Content-Type: application/json');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Handle GET request (Fetch types)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT type_id, type_name FROM type";
    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch types"]);
        exit;
    }

    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }

    echo json_encode($types);
    exit;
}

// Handle POST request (Add type to question)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    $qid = isset($_POST['qid']) ? intval($_POST['qid']) : 0;
    $type_id = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;

    if ($qid <= 0 || $type_id <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid question or type ID"]);
        exit;
    }

    // Insert into `question_type` table
    $stmt = $conn->prepare("INSERT INTO question_type (qid, type_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $qid, $type_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Type added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add type"]);
    }

    $stmt->close();
    exit;
}

// If request method is not GET or POST
http_response_code(405);
echo json_encode(["error" => "Method Not Allowed"]);
?>

