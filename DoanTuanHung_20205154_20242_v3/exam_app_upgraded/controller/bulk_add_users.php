<?php
include 'db_conn.php'; // DB connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csvFile'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$file = $_FILES['csvFile']['tmp_name'];
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

$handle = fopen($file, 'r');
if (!$handle) {
    echo json_encode(['success' => false, 'message' => 'Failed to open file']);
    exit;
}

$added = 0;
$skipped = 0;

$lineNum = 0;

while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
    $lineNum++;

    // Skip header
    if ($lineNum === 1)
        continue;

    if (count($data) < 2) {
        $skipped++;
        continue;
    }

    $user_name = trim($data[0]);
    $class_name = trim($data[1]);

    if (empty($user_name) || empty($class_name)) {
        $skipped++;
        continue;
    }

    // Get user_id
    $userStmt = $conn->prepare("SELECT id FROM users WHERE user_name = ?");
    $userStmt->bind_param("s", $user_name);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();

    if (!$user) {
        $skipped++;
        continue;
    }
    $user_id = $user['id'];

    // Get class_id
    $classStmt = $conn->prepare("SELECT class_id FROM class WHERE class_name = ?");
    $classStmt->bind_param("s", $class_name);
    $classStmt->execute();
    $classResult = $classStmt->get_result();
    $class = $classResult->fetch_assoc();

    if (!$class) {
        $skipped++;
        continue;
    }
    $class_id = $class['class_id'];

    // Check for existing assignment
    $checkStmt = $conn->prepare("SELECT * FROM users_class WHERE user_id = ? AND class_id = ?");
    $checkStmt->bind_param("ii", $user_id, $class_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $skipped++;
        continue;
    }

    // Insert
    $insertStmt = $conn->prepare("INSERT INTO users_class (user_id, class_id) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $user_id, $class_id);
    if ($insertStmt->execute()) {
        $added++;
    } else {
        $skipped++;
    }
}

fclose($handle);
$conn->close();

echo json_encode([
    'success' => true,
    'message' => "CSV Import complete. Added: $added, Skipped: $skipped"
]);
