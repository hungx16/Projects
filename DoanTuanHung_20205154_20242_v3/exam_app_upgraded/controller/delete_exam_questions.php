<?php
include 'db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['qids'], $data['eid']) && is_array($data['qids'])) {
    $eid = intval($data['eid']);
    $qids = array_map('intval', $data['qids']);
    $qidString = implode(',', $qids);

    // Delete only from the exam_question table for this eid
    $stmt = $conn->prepare("DELETE FROM exam_questions WHERE eid = ? AND qid IN ($qidString)");
    $stmt->bind_param("i", $eid);
    $stmt->execute();

    // 2. Count remaining questions in this exam
    $stmt = $conn->prepare("SELECT COUNT(*) FROM exam_questions WHERE eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $stmt->bind_result($newTotal);
    $stmt->fetch();
    $stmt->close();

    // 3. Update quiz.total
    $stmt = $conn->prepare("UPDATE quiz SET total = ? WHERE eid = ?");
    $stmt->bind_param("ii", $newTotal, $eid);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}