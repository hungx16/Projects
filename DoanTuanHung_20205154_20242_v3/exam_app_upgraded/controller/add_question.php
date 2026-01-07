// Create connection
<?php

try {
    ob_clean();
    header('Content-Type: application/json');

    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test_db"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $questionTitle = $_POST['questionTitle'];
        $questionType = $_POST['questionType'];
        $numberOfChoices = $_POST['numberOfChoices'];
        $choices = $_POST['choices'];
        $correctAnswer = strtoupper($_POST['correctAnswer']);
        $score = isset($_POST['score']) ? (int) $_POST['score'] : 1;
        if ($score < 1) {
            throw new Exception("Score must be at least 1.");
        }

        $result = $conn->query("SELECT MAX(qid) AS max_id FROM question_bank");
        $row = $result->fetch_assoc();
        $maxId = $row['max_id'] ?? 0;
        $conn->query("ALTER TABLE question_bank AUTO_INCREMENT = " . ($maxId + 1));


        // Step 1: Insert into question_bank
        $stmt = $conn->prepare("INSERT INTO question_bank (question_title, number_of_choices, score) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $questionTitle, $numberOfChoices, $score);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting question: " . $stmt->error);
        }
        $questionId = $conn->insert_id;

        // Step 2: Handle question type
        $stmtCheckType = $conn->prepare("SELECT type_id FROM type WHERE type_name = ?");
        $stmtCheckType->bind_param("s", $questionType);
        $stmtCheckType->execute();
        $result = $stmtCheckType->get_result();

        if ($result->num_rows > 0) {
            $typeRow = $result->fetch_assoc();
            $typeId = $typeRow['type_id'];
        } else {
            $stmtInsertType = $conn->prepare("INSERT INTO type (type_name) VALUES (?)");
            $stmtInsertType->bind_param("s", $questionType);
            if (!$stmtInsertType->execute()) {
                throw new Exception("Failed to insert type: " . $stmtInsertType->error);
            }
            $typeId = $conn->insert_id;
        }

        // Step 3: Link the question to the type
        $stmtLinkType = $conn->prepare("INSERT INTO question_type (qid, type_id) VALUES (?, ?)");
        $stmtLinkType->bind_param("ii", $questionId, $typeId);
        if (!$stmtLinkType->execute()) {
            throw new Exception("Failed to link question to type: " . $stmtLinkType->error);
        }

        // Step 4: Insert choices
        $stmtOption = $conn->prepare("INSERT INTO options (qid, `option`, isCorrect) VALUES (?, ?, ?)");
        foreach ($choices as $index => $choice) {
            $isCorrect = ($correctAnswer == chr(65 + $index)) ? 1 : 0;
            $stmtOption->bind_param("isi", $questionId, $choice, $isCorrect);
            if (!$stmtOption->execute()) {
                throw new Exception("Failed to insert choice: " . $stmtOption->error);
            }
        }

        // Success response
        echo json_encode([
            "status" => "success",
            "message" => "Question added successfully!",
            "question" => [
                "id" => $questionId,
                "title" => $questionTitle,
                "type" => $questionType,
                "choices" => $choices,
                "correctAnswer" => $correctAnswer
            ]
        ]);
        exit(); // 👈 Stop script execution here!
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
//del single question section
if (isset($_GET['qid'])) {
    $qid = intval($_GET['qid']);

    $query = "DELETE FROM question_bank WHERE qid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $qid);

    if ($stmt->execute()) {
        echo "Question deleted successfully.";
    } else {
        echo "Error deleting question: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: /exam_app_upgraded/question_bank.php"); // Redirect back to the question bank page
    exit();
} else {
    echo "Invalid request.";
}
?>