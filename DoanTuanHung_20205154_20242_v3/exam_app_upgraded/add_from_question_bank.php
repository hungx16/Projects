<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['eid'])) {
    $eid = intval($_GET['eid']);
} else {
    die("Exam ID is missing!");
}
// Check if form is submitted

?>
<!--query part for printing exam question -->
<?php
$filterType = '';
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filterType = $conn->real_escape_string($_GET['type']); // Sanitize input
}

$query = "SELECT 
        qb.qid,
        qb.question_title AS question,
        o.option AS option_text,
        o.isCorrect AS is_correct,
        GROUP_CONCAT(DISTINCT t.type_name ORDER BY t.type_name SEPARATOR ', ') AS types
    FROM 
        question_bank qb
    LEFT JOIN 
        options o ON qb.qid = o.qid
    LEFT JOIN 
        question_type qt ON qb.qid = qt.qid
    LEFT JOIN 
        type t ON qt.type_id = t.type_id";

if (!empty($filterType)) {
    $query .= " WHERE t.type_name = '$filterType'";
}

$query .= " GROUP BY qb.qid, o.optionid ORDER BY qb.qid, o.optionid";

$result = $conn->query($query) or die('Error');
$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $qid = $row['qid'];
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'question' => $row['question'],
                'types' => $row['types'] ?: 'No types', // Default to "No types" if no types exist
                'options' => [],
                'correct_answer' => null
            ];
        }
        $questions[$qid]['options'][] = $row['option_text'];
        if ($row['is_correct'] == 1) {
            $questions[$qid]['correct_answer'] = $row['option_text'];
        }
    }
} else {
    echo "No questions found.";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar With Bootstrap</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENZdJFG2ZtPvbNQtjXwLE9P7V3zlEx8trhF5PpvCX4jI/7OObRtXlcU3iu7FZnN6" crossorigin="anonymous">

    <link rel="stylesheet" href="/exam_app_upgraded/sidebar/style.css">
</head>

<body>
    <div class="wrapper">
        <!-- sidebarframe -->
        <?php include "css/sidebar_admin.php" ?>
        <!-- end of sidebar frame -->
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <?php
                $title = "Question Bank";
                echo "<h3 style='text-align: center;'>$title</h3>";
                ?>
            </div>


            <!-- input form -->
            <div class="container" style="margin-top: 50px; text-align: center;">
                <!-- Center the H3 -->
                <h3 style="margin-bottom: 20px;">Add Question to Exam (EID: <?php echo htmlspecialchars($eid); ?>)</h3>

                <!-- Center the Form -->
                <form method="POST" action="add_from_question_bank.php?eid=<?php echo $eid; ?>"
                    style="display: inline-block; text-align: left;">
                    <div class="form-group row" style="margin: 0 auto;">
                        <div class="col-md-8" style="margin: 0 auto;">
                            <input type="number" class="form-control" name="qid" placeholder="Enter Question ID"
                                required>
                        </div>
                        <div class="col-md-4" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $qid = intval($_POST['qid']);

                // Fetch question details from the question_bank table
                $query = "SELECT question_title, number_of_choices, score FROM question_bank WHERE qid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $qid);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $question = $result->fetch_assoc();
                    $question_title = $question['question_title'];
                    $number_of_choices = $question['number_of_choices'];
                    $score = $question['score'];

                    // Insert question into the exam_questions table
                    $insert = "INSERT INTO exam_questions (eid, qid, question_title, number_of_choices, score) 
               VALUES (?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert);
                    $stmt_insert->bind_param("iissi", $eid, $qid, $question_title, $number_of_choices, $score);

                    if ($stmt_insert->execute()) {
                        $updateTotal = "UPDATE quiz 
                            SET total = (
                                SELECT COUNT(*) FROM exam_questions WHERE eid = ?
                            )
                            WHERE eid = ?";
                        $stmt_total = $conn->prepare($updateTotal);
                        $stmt_total->bind_param("ii", $eid, $eid);
                        $stmt_total->execute();
                        $stmt_total->close();
                        echo "<div class='alert alert-success'>Question successfully added to the exam!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $stmt_insert->error . "</div>";
                    }
                    $stmt_insert->close();
                } else {
                    echo "<div class='alert alert-warning'>Question ID not found in the Question Bank!</div>";
                }
                $stmt->close();
            }
            ?>
            <!-- filter form-->

            <!-- Filter form-->
            <form method="GET" action="" style="text-align: center; margin-bottom: 20px;">
                <input type="hidden" name="eid" value="<?php echo htmlspecialchars($eid); ?>">
                <label for="type">Filter by Type: </label>
                <select name="type" id="type" style="padding: 5px;">
                    <option value="">Select Type</option>
                    <?php
                    // Fetch distinct types for the dropdown
                    $typeQuery = "SELECT DISTINCT type_name FROM type";
                    $typeResult = $conn->query($typeQuery);
                    while ($row = $typeResult->fetch_assoc()) {
                        $selected = ($filterType === $row['type_name']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['type_name']) . "' $selected>" . htmlspecialchars($row['type_name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" style="padding: 5px 10px; margin-left: 10px;">Filter</button>
            </form>
            <!-- print exam questions -->
            <!-- Print Exam Questions -->
            <?php if (!empty($questions)): ?>
                <div class="container">
                    <div class="row">
                        <?php if (!empty($questions)): ?>
                            <?php foreach ($questions as $qid => $details): ?>
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                Question <?= htmlspecialchars($qid); ?>
                                                (<?= htmlspecialchars($details['types']); ?>):
                                                <input type="checkbox" class="delete-checkbox"
                                                    data-qid="<?= htmlspecialchars($qid); ?>" style="display: none;">
                                            </h5>
                                            <p class="card-text"><?= htmlspecialchars($details['question']); ?></p>
                                            <ul class="list-group mb-3">
                                                <?php foreach ($details['options'] as $option): ?>
                                                    <li class="list-group-item"><?= htmlspecialchars($option); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <p class="text-success"><strong>Correct Answer:</strong>
                                                <?= htmlspecialchars($details['correct_answer']); ?></p>


                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">No questions available.</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <p class="text-center">No questions available.</p>
            <?php endif; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
        <script src="/exam_app_upgraded/js/script.js"></script>