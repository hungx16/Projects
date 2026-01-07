<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs

    $examId = isset($_POST['examId']) ? intval($_POST['examId']) : 0;

    // Debugging: Print the examId
    echo "Exam ID received: " . $examId;
    error_log("Exam ID received: " . $examId); // Logs it in the server log for debugging

    // Check if examId is invalid
    if ($examId === 0) {
        die("Error: Invalid Exam ID received.");
    }
    $eid = $_POST['examId'];
    // From the hidden input field
    $questionTitle = $_POST['questionTitle'];
    $questionType = $_POST['questionType'];
    $numberOfChoices = (int) $_POST['numberOfChoices'];
    $choices = $_POST['choices']; // Array of choices
    $correctAnswer = strtoupper(trim($_POST['correctAnswer'])); // e.g., "A"

    // Validate the correct answer is within the range of provided choices
    $correctIndex = ord($correctAnswer) - 65; // Convert A, B, C... to 0, 1, 2...
    if ($correctIndex < 0 || $correctIndex >= $numberOfChoices) {
        die("Invalid correct answer. It must be within the range of choices.");
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Start transaction
        $conn->begin_transaction();

        // Step 1: Insert question into the `question_bank` table
        $stmt = $conn->prepare("INSERT INTO question_bank (question_title, number_of_choices, score) VALUES (?, ?, ?)");
        $score = isset($_POST['score']) ? intval($_POST['score']) : 1;
        if ($score <= 0) {
            throw new Exception("Score must be greater than 0.");
        } // Default score, can be customized
        $stmt->bind_param("sii", $questionTitle, $numberOfChoices, $score);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert question into question_bank: " . $stmt->error);
        }

        // Get the ID of the newly inserted question
        $questionId = $conn->insert_id;

        // Step 2: Link question with a single type in `question_type` table

        // Check if the type already exists in the `type` table
        $stmtCheckType = $conn->prepare("SELECT type_id FROM type WHERE type_name = ?");
        $stmtCheckType->bind_param("s", $questionType);
        $stmtCheckType->execute();
        $result = $stmtCheckType->get_result();

        if ($result->num_rows > 0) {
            // If the type exists, get the type_id
            $typeRow = $result->fetch_assoc();
            $typeId = $typeRow['type_id'];
        } else {
            // If the type does not exist, create a new type
            $stmtInsertType = $conn->prepare("INSERT INTO type (type_name) VALUES (?)");
            $stmtInsertType->bind_param("s", $questionType);
            if (!$stmtInsertType->execute()) {
                throw new Exception("Failed to insert type into type table: " . $stmtInsertType->error);
            }
            // Get the ID of the newly inserted type
            $typeId = $conn->insert_id;
        }

        // Link the question to the type in the `question_type` table
        $stmtLinkType = $conn->prepare("INSERT INTO question_type (qid, type_id) VALUES (?, ?)");
        $stmtLinkType->bind_param("ii", $questionId, $typeId);
        if (!$stmtLinkType->execute()) {
            throw new Exception("Failed to insert question type into question_type: " . $stmtLinkType->error);
        }


        // Step 3: Insert choices into the `options` table
        foreach ($choices as $index => $choiceText) {
            $isCorrect = ($index == $correctIndex) ? 1 : 0; // $correctIndex is the index of the correct answer

            $stmtOptions = $conn->prepare("INSERT INTO options (qid, `option`, isCorrect) VALUES (?, ?, ?)");
            $stmtOptions->bind_param("isi", $questionId, $choiceText, $isCorrect);

            if (!$stmtOptions->execute()) {
                throw new Exception("Failed to insert choice into options: " . $stmtOptions->error);
            }
        }

        // Step 4: Map question to the exam in the `exam_questions` table
        $stmtExamQuestions = $conn->prepare("INSERT INTO exam_questions (eid, qid, question_title, number_of_choices, score) VALUES (?, ?, ?, ?, ?)");
        $stmtExamQuestions->bind_param("iisii", $examId, $questionId, $questionTitle, $numberOfChoices, $score);

        if (!$stmtExamQuestions->execute()) {
            throw new Exception("Failed to insert question into exam_questions: " . $stmtExamQuestions->error);
        }

        // Commit transaction
        $conn->commit();
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
        echo "Question successfully added to the exam!";
    } catch (Exception $e) {
        // Rollback transaction on failure
        $conn->rollback();
        die("Error: " . $e->getMessage());
    } finally {
        // Close statements and connection
        $stmt->close();
        $conn->close();
    }
} else if (isset($_GET['qid'])) {
    $qid = intval($_GET['qid']);

    $query = "DELETE FROM exam_questions WHERE qid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $qid);

    if ($stmt->execute()) {
        echo "Question deleted successfully.";
    } else {
        echo "Error deleting question: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: manage_exam.php"); // Redirect back to the question bank page
    exit();
} else {
    echo "Invalid Request!";
}
?>

<!--
+ Try to add question through bank by accessing question bank and let user input id (done)
+ Divide question bank into types (by filter functions) (done)
=

=> Qb FATHER of exam_questions



+ Add "Modify" functions 
+ Some other functions
-->