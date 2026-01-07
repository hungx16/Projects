<?php
session_start();
include_once 'db_conn.php';

$user = $_SESSION['user_name'];
$eid = $_POST['eid'];
$totalQuestions = 0;
$correct = 0;
$wrong = 0;
$score = 0;
$deleteOldHistory = "DELETE FROM history WHERE user_name='$user' AND eid=$eid";
mysqli_query($conn, $deleteOldHistory);
$deleteOldRank = "DELETE FROM rank WHERE user_name='$user' AND eid=$eid";
mysqli_query($conn, $deleteOldRank);
// First, insert into history to ensure the parent row exists
$insertHistory = "INSERT INTO history (user_name, eid, score, total_questions, correct, wrong, date)
    VALUES ('$user', $eid, 0, 0, 0, 0, NOW())";
mysqli_query($conn, $insertHistory);

// Insert into history
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') !== false) {
        $qid = str_replace('question_', '', $key);
        $totalQuestions++;

        // Get correct answer
        $query = "SELECT * FROM options WHERE qid=$qid AND isCorrect=1";
        $res = mysqli_query($conn, $query);
        $correctAnswer = mysqli_fetch_assoc($res)['optionid'];

        if ($value == $correctAnswer) {
            $correct++;
            $score += 1; // Adjust according to scoring logic
            $isCorrect = 1;
        } else {
            $wrong++;
            $isCorrect = 0;
        }

        // Save history details
        $insertHistoryDetails = "INSERT INTO history_details (eid, qid, answer_id, answer_chosen, correct_answer, score, isCorrect, user_name)
        VALUES ($eid, $qid, $value, '$value', '$correctAnswer', 1, $isCorrect, '$user')";

        mysqli_query($conn, $insertHistoryDetails);
    }
}
// Now update history with actual data
$updateHistory = "UPDATE history SET score=$score, total_questions=$totalQuestions, correct=$correct, wrong=$wrong 
                  WHERE user_name='$user' AND eid=$eid";
mysqli_query($conn, $updateHistory);

// Update rank
$updateRank = "
INSERT INTO rank (user_name, score, time, eid, class_name, title)
VALUES (
    '$user',
    $score,
    NOW(),
    $eid,
    (SELECT c.class_name 
     FROM quiz q 
     JOIN class c ON q.class_id = c.class_id 
     WHERE q.eid = $eid),
    (SELECT title FROM quiz WHERE eid = $eid)
)";
mysqli_query($conn, $updateRank);



header("location:/exam_app_upgraded/history.php");
