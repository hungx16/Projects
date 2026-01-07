<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['assign']) && !isset($_POST['delete'])) {
        die("Form submitted without expected action. Please check form button names.");
    }
    $class_id = $_POST['class_id'];
    if (isset($_POST['assign'])) {
        $user_id = $_POST['user_id'];
        $class_id = $_POST['class_id'];

        // Only allow assigning a lecturer
        // Get real_name and role
        $stmt = $conn->prepare("SELECT real_name, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($real_name, $role);
        $stmt->fetch();
        $stmt->close();

        if ($role !== 'lecturer') {
            die("Only lecturers can be assigned to classes.");
        }
        // Assign lecturer to class
        $stmt = $conn->prepare("UPDATE class SET lecturer_name = ?, lecturer_id = ? WHERE class_id = ?");
        $stmt->bind_param("sii", $real_name, $user_id, $class_id); // ✅ Correct: name, id, class_id

        $stmt->execute();
        $stmt->close();

        // Send notification
        $message = "You have been assigned as a lecturer to a class.";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at)
            VALUES (?, ?, 0, NOW())");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: /exam_app_upgraded/home_headmaster.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $user_id = $_POST['user_id'];
        $real_name = $_POST['lecturer_name'];
        // Clear lecturer_name in class table
        // Get user_id from real_name

        if ($user_id) {
            // Clear lecturer_name in class table
            $stmt = $conn->prepare("UPDATE class SET lecturer_name = NULL, lecturer_id = NULL WHERE class_id = ?");
            $stmt->bind_param("i", $class_id);
            $stmt->execute();
            $stmt->close();

            // Send notification
            $message = "You have been removed from a class.";
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at)
            VALUES (?, ?, 0, NOW())");
            $stmt->bind_param("is", $user_id, $message);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: /exam_app_upgraded/home_headmaster.php");
        exit();
    }
}
?>