<?php
header('Content-Type: application/json');
include 'db_conn.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $qid = intval($_POST['qid']);
    $questionTitle = $_POST['questionTitle'];
    // New question type
    $numberOfChoices = intval($_POST['numberOfChoices']);
    $choices = $_POST['choices']; // Array of choices
    $correctAnswer = strtoupper($_POST['correctAnswer']); // Ensure it's in uppercase
    $score = 1; // Default score (can be adjusted)

    $conn->begin_transaction(); // Start transaction

    try {
        // **Step 1: Update the question in `question_bank`**
        $stmt = $conn->prepare("UPDATE question_bank SET question_title = ?, number_of_choices = ?, score = ? WHERE qid = ?");
        $stmt->bind_param("siii", $questionTitle, $numberOfChoices, $score, $qid);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update question_bank: " . $stmt->error);
        }


        // **Step 3: Update options in `options` table**
        // Delete old options
        $stmtDeleteOptions = $conn->prepare("DELETE FROM options WHERE qid = ?");
        $stmtDeleteOptions->bind_param("i", $qid);
        if (!$stmtDeleteOptions->execute()) {
            throw new Exception("Failed to delete old options: " . $stmtDeleteOptions->error);
        }

        // Insert new options
        $stmtInsertOption = $conn->prepare("INSERT INTO options (qid, `option`, isCorrect) VALUES (?, ?, ?)");
        foreach ($choices as $index => $choice) {
            $isCorrect = ($correctAnswer == chr(65 + $index)) ? 1 : 0; // Compare with A, B, C, etc.
            $stmtInsertOption->bind_param("isi", $qid, $choice, $isCorrect);
            if (!$stmtInsertOption->execute()) {
                throw new Exception("Failed to insert new options: " . $stmtInsertOption->error);
            }
        }

        $conn->commit(); // Commit transaction
        echo json_encode(["success" => true, "message" => "Question updated successfully!"]);

    } catch (Exception $e) {
        $conn->rollback(); // Rollback changes if there's an error
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    $conn->close();
}
?>