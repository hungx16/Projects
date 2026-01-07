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
$filterEid = '';
if (isset($_GET['eid']) && !empty($_GET['eid'])) {
    $filterEid = $conn->real_escape_string($_GET['eid']); // Sanitize input
}

$query = "SELECT 
        eq.eid,
        qb.qid,
        qb.question_title AS question,
        qb.score AS score,
        o.option AS option_text,
        o.isCorrect AS is_correct,
        GROUP_CONCAT(DISTINCT t.type_name ORDER BY t.type_name SEPARATOR ', ') AS types
    FROM 
        exam_questions eq
    JOIN 
        question_bank qb ON eq.qid = qb.qid
    LEFT JOIN 
        options o ON qb.qid = o.qid
    LEFT JOIN 
        question_type qt ON qb.qid = qt.qid
    LEFT JOIN 
        type t ON qt.type_id = t.type_id
    WHERE 
        eq.eid = '$filterEid'
    GROUP BY 
        qb.qid, o.optionid 
    ORDER BY 
        qb.qid, o.optionid";

$result = $conn->query($query) or die('Error');
$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $qid = $row['qid'];
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'question' => $row['question'],
                'score' => $row['score'],
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
}
$total_score = 0;
foreach ($questions as $details) {
    $total_score += isset($details['score']) ? $details['score'] : 0;
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
                <?php
                if (isset($_GET['eid'])) {
                    $eid = intval($_GET['eid']);
                    $title = "Questions of exam " . $eid;
                    echo "<h3 style='text-align: center;'>$title</h3>";
                    echo "<h4 style='text-align: center; font-weight: bold;'>Total Score: $total_score</h4>";
                }
                ?>
            </div>
            <div class="container mt-5 text-center">
                <!-- Button to Open Modal -->
                <button id="delete-multiple-btn" class="btn btn-danger">Delete Multiple Questions</button>
                <div id="delete-options" style="display: none;">
                    <p>Choose questions to delete below:</p>
                    <button id="confirm-delete" class="btn btn-danger">Confirm Delete</button>
                    <button id="cancel-delete" class="btn btn-secondary">Cancel</button>
                </div>
            </div>


            <!-- print question -->
            <?php if (!empty($questions)): ?>
                <div class="container">
                    <div class="row">
                        <?php if (!empty($questions)): ?>
                            <?php foreach ($questions as $qid => $details): ?>
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <span>Question <?= htmlspecialchars($qid); ?>
                                                    (<?= htmlspecialchars($details['score']); ?> pts):</span>
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
            <!-- end of main content -->

            <!-- handle delete multi qquestions -->

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
                crossorigin="anonymous"></script>
            <script src="js/script.js"></script>
            <script src="js/deleteMultipleExamQuestion.js"></script>