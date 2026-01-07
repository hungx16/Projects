<?php
include 'db_conn.php'; // Make sure this file connects to your DB

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;

    $user_name = isset($data['user_name']) ? trim($data['user_name']) : null;
    $class_name = isset($data['class_name']) ? trim($data['class_name']) : null;
    $action = isset($data['action']) ? $data['action'] : 'add'; // default is add

    if (empty($user_name) || empty($class_name)) {
        echo json_encode(["success" => false, "message" => "Both name and class are required."]);
        exit();
    }

    // Find user ID
    $userQuery = $conn->prepare("SELECT id FROM users WHERE user_name = ?");
    $userQuery->bind_param("s", $user_name);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $user = $userResult->fetch_assoc();

    if (!$user) {
        echo json_encode(["success" => false, "message" => "User not found."]);
        exit();
    }
    $user_id = $user['id'];

    // Find class ID
    $classQuery = $conn->prepare("SELECT class_id FROM class WHERE class_name = ?");
    $classQuery->bind_param("s", $class_name);
    $classQuery->execute();
    $classResult = $classQuery->get_result();
    $class = $classResult->fetch_assoc();

    if (!$class) {
        echo json_encode(["success" => false, "message" => "Class not found."]);
        exit();
    }
    $class_id = $class['class_id'];

    if ($action === 'add') {
        // Check if already exists
        $checkQuery = $conn->prepare("SELECT * FROM users_class WHERE user_id = ? AND class_id = ?");
        $checkQuery->bind_param("ii", $user_id, $class_id);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "User is already in this class."]);
            exit();
        }

        $insertQuery = $conn->prepare("INSERT INTO users_class (user_id, class_id) VALUES (?, ?)");
        $insertQuery->bind_param("ii", $user_id, $class_id);

        if ($insertQuery->execute()) {
            $message = "You have been added to class: " . $class_name;
            $notifQuery = $conn->prepare("INSERT INTO notifications (user_id, message,is_read, created_at) VALUES (?, ?, 0, NOW())");
            $notifQuery->bind_param("is", $user_id, $message);
            $notifQuery->execute();
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add user to class."]);
        }

    } else if ($action === 'delete') {
        $deleteQuery = $conn->prepare("DELETE FROM users_class WHERE user_id = ? AND class_id = ?");
        $deleteQuery->bind_param("ii", $user_id, $class_id);

        if ($deleteQuery->execute()) {
            $message = "You have been removed from class: " . $class_name;
            $notifQuery = $conn->prepare("INSERT INTO notifications (user_id, message,is_read, created_at) VALUES (?, ?,0, NOW())");
            $notifQuery->bind_param("is", $user_id, $message);
            $notifQuery->execute();
            echo json_encode(["success" => true, "message" => "User removed from class and notified."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to remove user from class."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>