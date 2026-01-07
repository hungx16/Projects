<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
    exit;
}
if (!isset($_SESSION['role'])) {
    header("location:login.php");
    exit;
}

$name = isset($_GET['username']) && $_SESSION['role'] === 'lecturer'
    ? $_GET['username']
    : $_SESSION['user_name'];
$role = $_SESSION['role'];
$eid = $_GET['eid'];
$query = "
SELECT 
    eq.qid, 
    qb.question_title, 
    hd.answer_chosen AS chosen_answer, 
    hd.isCorrect, 
    hd.answer_id,
    (SELECT option FROM options WHERE qid = eq.qid AND isCorrect = 1) AS correct_answer,
    (SELECT GROUP_CONCAT(optionid, '|', option) FROM options WHERE qid = eq.qid) AS all_options
FROM exam_questions eq
JOIN question_bank qb ON eq.qid = qb.qid
LEFT JOIN history_details hd 
    ON hd.qid = eq.qid AND hd.user_name = '$name' AND hd.eid = $eid
WHERE eq.eid = $eid
";

$result = mysqli_query($conn, $query);
?>

<!--FRAME of the website -->
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
    <link rel="stylesheet" href="sidebar/style.css">

</head>

<body>
    <div class="wrapper">
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'lecturer') {
            include "css/sidebar_admin.php";
        } else {
            include "css/sidebar.php";
        }
        ?>
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h1>
                    Result of <?php echo htmlspecialchars($name); ?>
                </h1>
            </div>
            <div class="container mt-5">
                <h2 class="text-center">Exam <?= htmlspecialchars($eid); ?> - Results</h2>

                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Question <?= htmlspecialchars($row['qid']); ?></h5>
                            <p class="card-text"><?= htmlspecialchars($row['question_title']); ?></p>

                            <!-- Answer Options -->
                            <ul class="list-group">
                                <?php
                                $options = explode(',', $row['all_options']);
                                foreach ($options as $optionData):
                                    list($optionId, $optionText) = explode('|', $optionData);
                                    $isAnswered = !is_null($row['answer_id']);
                                    $isChosen = ($optionId == $row['answer_id']);
                                    $isCorrect = $row['isCorrect'];
                                    ?>
                                    <li
                                        class="list-group-item 
                                        <?= $isChosen ? ($isCorrect ? 'correct' : 'wrong') : ($isAnswered ? '' : 'unanswered'); ?>">
                                        <?= $isChosen
                                            ? ($isCorrect ? '✅' : '❌')
                                            : ($isAnswered ? '🔲' : '🔲'); ?>
                                        <?= htmlspecialchars($optionText); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Correct Answer -->
                            <p class="correct-answer"><strong>Correct Answer:</strong>
                                <?= htmlspecialchars($row['correct_answer']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
                crossorigin="anonymous"></script>
            <script src="js/script.js"></script>
</body>

</html>