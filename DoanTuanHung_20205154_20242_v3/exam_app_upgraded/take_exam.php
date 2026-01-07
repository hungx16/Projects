<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['user_name'];

    include_once 'db_conn.php';
}
$eid = isset($_GET['eid']) ? intval($_GET['eid']) : 0;
if ($eid == 0) {
    echo "Invalid Exam!";
    exit();
}

// Fetch exam duration
$query = "SELECT exam_time FROM quiz WHERE eid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $eid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$examTime = isset($row['exam_time']) ? $row['exam_time'] : 600;
// Fetch exam questions
$sql = "SELECT * FROM exam_questions WHERE eid = $eid";
$result = mysqli_query($conn, $sql);

$questions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $qid = $row['qid'];
    $questions[$qid] = [
        'question' => $row['question_title'],
        'score' => $row['score'],
        'options' => [],
    ];

    // Fetch options for this question
    $optQuery = "SELECT * FROM options WHERE qid = $qid";
    $optResult = mysqli_query($conn, $optQuery);
    while ($opt = mysqli_fetch_assoc($optResult)) {
        $questions[$qid]['options'][] = [
            'id' => $opt['optionid'],
            'text' => $opt['option']
        ];
    }



}
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
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h1>
                    Welcome, user
                </h1>
                <h3 id="timer" style="font-weight: bold; color: red;"></h3>
            </div>
            <!-- timer set -->
            <script>
                let timeLeft = <?php echo $examTime; ?>; // 10 minutes in seconds

                function updateTimer() {
                    let minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;

                    // Ensure seconds always show as two digits (e.g., 05 instead of 5)
                    let timeDisplay = `${minutes}:${seconds < 10 ? '0' : ''}${seconds} minutes remaining`;

                    // Update the timer display
                    document.getElementById("timer").innerText = timeDisplay;

                    // Auto-submit when time reaches 0
                    if (timeLeft <= 0) {
                        alert("Time is up! Submitting the exam now.");
                        document.getElementById("examForm").submit();
                    } else {
                        timeLeft--;
                        setTimeout(updateTimer, 1000);
                    }
                }

                // Start the timer on page load
                updateTimer();
            </script>

            <!-- Question Navigation -->
            <div class="question-nav">
                <?php $counter = 1; ?>
                <?php foreach ($questions as $index => $details): ?>
                    <div class="question-circle" data-qid="<?= $counter; ?>" onclick="jumpToQuestion(<?= $counter; ?>)">
                        <?= $counter; ?>
                    </div>
                    <?php $counter++; ?>
                <?php endforeach; ?>
            </div>

            <style>
                .question-nav {
                    display: flex;
                    justify-content: center;
                    /* Center horizontally */
                    align-items: center;
                    gap: 10px;
                    margin: 10px 0;
                }

                .question-circle {
                    width: 30px;
                    height: 30px;
                    background-color: lightgray;
                    border-radius: 50%;
                    text-align: center;
                    line-height: 30px;
                    cursor: pointer;
                    font-weight: bold;
                }

                .question-circle.answered {
                    background-color: green;
                    /* Highlight answered */
                    color: white;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('input[type="radio"]').forEach(radio => {
                        radio.addEventListener('change', function () {
                            let qid = this.name.split('_')[1];
                            document.querySelector(`.question-circle[data-qid="${qid}"]`).classList.add('answered');
                        });
                    });
                });

                function jumpToQuestion(qid) {
                    let questionCard = document.querySelector(`[name="question_${qid}"]`);
                    if (questionCard) {
                        questionCard.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            </script>


            <!-- print alll exam questions -->
            <div class="container mt-5">
                <h2 class="text-center">Exam <?= htmlspecialchars($eid); ?></h2>
                <form action="/exam_app_upgraded/controller/submit_exam.php" method="POST" id="examForm">
                    <input type="hidden" name="eid" value="<?= htmlspecialchars($eid); ?>">

                    <!-- Display Questions -->
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $qid => $details): ?>
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Question <?= htmlspecialchars($qid); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($details['question']); ?></p>

                                    <!-- Answer Options -->
                                    <ul class="list-group">
                                        <?php foreach ($details['options'] as $option): ?>
                                            <li class="list-group-item">
                                                <input type="radio" name="question_<?= htmlspecialchars($qid); ?>"
                                                    value="<?= htmlspecialchars($option['id']); ?>" required>
                                                <?= htmlspecialchars($option['text']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" onclick="return confirmSubmit();">
                                Submit Exam
                            </button>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No questions available for this exam.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
        <script src="js/script.js"></script>

        <script>
            function confirmSubmit() {
                return confirm("Are you sure you want to submit the exam?");
            }
        </script>
</body>

</html>
<!-- end of frame -->