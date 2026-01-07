<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['name'];

    include_once 'db_conn.php';
}
?>
<!--query part for printing exam question -->
<?php
$filterType = '';
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filterType = $conn->real_escape_string($_GET['type']); // Sanitize input
}
$typeOptions = "";
$result = $conn->query("SELECT type_name FROM type ORDER BY type_name ASC");
while ($row = $result->fetch_assoc()) {
    $typeName = htmlspecialchars($row['type_name']);
    $typeOptions .= "<option value=\"$typeName\">$typeName</option>\n";
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
<!-- end query, begin question bank -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar With Bootstrap</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="sidebar/style.css">
</head>

<body>
    <div class="wrapper">
        <!-- sidebarframe -->
        <?php include "css/sidebar_admin.php" ?>
        <!-- end of sidebar frame -->
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <?php $title = "Question Bank";
                echo "<h3 style='text-align: center;'>$title</h3>";
                ?>
            </div>
            <div class="container mt-4">
                <!-- filter form -->
                <form method="GET" action="" style="text-align: center; margin-bottom: 20px;">
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
                <!-- Button to Open Modal -->
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addQuestionModal">
                        Add Question
                    </button>
                </div>
                <!-- delete question button -->
                <div class="text-center mb-4">
                    <button id="delete-multiple-btn" class="btn btn-danger">Delete Multiple Questions</button>
                    <div id="delete-options" style="display: none;">
                        <p>Choose questions to delete below:</p>
                        <button id="confirm-delete" class="btn btn-danger">Confirm Delete</button>
                        <button id="cancel-delete" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>

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
                                                <!-- Buttons Aligned to Left -->
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary btn-sm add-type-btn" data-bs-toggle="modal"
                                                        data-bs-target="#addTypeModal" data-qid="<?= htmlspecialchars($qid); ?>">
                                                        Add Type
                                                    </button>
                                                    <button class="btn btn-warning btn-sm modify-btn" data-bs-toggle="modal"
                                                        data-bs-target="#modifyQuestionModal"
                                                        data-qid="<?= htmlspecialchars($qid); ?>"
                                                        data-title="<?= htmlspecialchars($details['question']); ?>"
                                                        data-num-choices="<?= count($details['options']); ?>"
                                                        data-correct="<?= htmlspecialchars($details['correct_answer']); ?>"
                                                        data-types="<?= htmlspecialchars($details['types']); ?>"
                                                        data-options="<?= htmlspecialchars(json_encode($details['options'])); ?>">
                                                        ✏ Modify
                                                    </button>
                                                    <a href="/exam_app_upgraded/controller/add_question.php?qid=<?= htmlspecialchars($qid); ?>"
                                                        class="btn btn-danger btn-sm delete-single"
                                                        onclick="return confirm('Are you sure you want to delete this question?');">
                                                        🗑 Delete
                                                    </a>
                                                </div>
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

</html>

<!-- 🚀 Step 2: Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addQuestionModalLabel">Add New Question</h4>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="questionForm">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="questionTitle" class="form-label">Question Title</label>
                        <input type="text" class="form-control" id="questionTitle" name="questionTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="score" class="form-label">Score</label>
                        <input type="number" class="form-control" id="score" name="score" value="1" min="1" required>
                    </div>
                    <!-- Question Type -->
                    <div class="mb-3">
                        <label for="questionType" class="form-label">Question Type</label>
                        <select class="form-select" id="questionType" name="questionType" required>
                            <option value="">Select Type...</option>
                            <?= $typeOptions ?>
                        </select>
                    </div>

                    <!-- Number of Choices -->
                    <div class="mb-3">
                        <label for="numberOfChoices" class="form-label">Number of Choices</label>
                        <select class="form-select" id="numberOfChoices" name="numberOfChoices" required>
                            <option value="">Select...</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <!-- Choices Container -->
                    <div id="choicesContainer"></div>

                    <!-- Correct Answer -->
                    <div class="mb-3" id="correctAnswerContainer" style="display: none;">
                        <label for="correctAnswer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="correctAnswer" name="correctAnswer"
                            placeholder="Enter correct answer (e.g., A, B, C)">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success">Add Question</button>

                </form>
            </div>
        </div>
    </div>
</div>


<!-- handle delete multi questions, check js -->


<!-- Modify Question Modal -->
<div class="modal fade" id="modifyQuestionModal" tabindex="-1" role="dialog" aria-labelledby="modifyQuestionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modifyQuestionModalLabel">Modify Question</h4>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modifyQuestionForm">
                    <input type="hidden" id="modifyQid" name="qid">

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="modifyQuestionTitle" class="form-label">Question Title</label>
                        <input type="text" class="form-control" id="modifyQuestionTitle" name="questionTitle" required>
                    </div>

                    <!-- Number of Choices -->
                    <div class="mb-3">
                        <label for="modifyNumberOfChoices" class="form-label">Number of Choices</label>
                        <select class="form-select" id="modifyNumberOfChoices" name="numberOfChoices" required>
                            <option value="">Select...</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>

                    <!-- Choices Container -->
                    <div id="modifyChoicesContainer"></div>

                    <!-- Correct Answer -->
                    <div class="mb-3">
                        <label for="modifyCorrectAnswer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="modifyCorrectAnswer" name="correctAnswer">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- check js for modify question handler -->


<!-- modal to add type -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-labelledby="addTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTypeModalLabel">Add Type to Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTypeForm">
                    <input type="hidden" name="qid" id="modalQid">
                    <div class="mb-3">
                        <label for="typeSelect" class="form-label">Select Type</label>
                        <select class="form-control" id="typeSelect" name="type_id" required>
                            <!-- Options will be populated via JavaScript -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Type</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- js part addtypetoquestion -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
<script src="js/script.js"></script>
<script src="js/addQuestion.js"></script>
<script src="js/modifyQuestion.js"></script>
<script src="js/deleteMultipleQuestion.js"></script>
<script src="js/addTypetoQuestion.js"></script>

<script src="js/addTypetoQuestion.js"></script>