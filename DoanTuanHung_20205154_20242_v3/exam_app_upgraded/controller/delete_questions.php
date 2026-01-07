<?php
include 'db_conn.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['qids']) && is_array($data['qids'])) {
    $qids = array_map('intval', $data['qids']); // Sanitize input
    $qidString = implode(",", $qids);

    $query = "DELETE FROM question_bank WHERE qid IN ($qidString)";
    $conn->query($query);

    // Ensure options and types related to questions are also deleted
    $conn->query("DELETE FROM options WHERE qid IN ($qidString)");
    $conn->query("DELETE FROM question_type WHERE qid IN ($qidString)");

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}
?>