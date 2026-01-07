<?php
include_once 'db_conn.php';
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['user_name'];
    include_once 'db_conn.php';
    $result_id = mysqli_query($conn, "SELECT id FROM users WHERE user_name = '" . mysqli_real_escape_string($conn, $name) . "'") or die("User lookup failed");
    $row_id = mysqli_fetch_assoc($result_id);
    $lecturer_id = $row_id['id'];
    // Fetch classes taught by this lecturer
    $classOptions = [];

    if ($lecturer_id) {
        $stmt = $conn->prepare("SELECT class_id, class_name FROM class WHERE lecturer_id = ?");
        $stmt->bind_param("i", $lecturer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $classOptions[] = $row;
        }
        $stmt->close();
    }

}
$current = date('Y-m-d H:i:s');
mysqli_query($conn, "
        UPDATE quiz 
        SET is_open = CASE
            WHEN start_time IS NOT NULL AND end_time IS NOT NULL THEN
                CASE 
                    WHEN '$current' BETWEEN start_time AND end_time THEN 1
                    ELSE 0
                END
            ELSE is_open
        END
    ");

?>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENZdJFG2ZtPvbNQtjXwLE9P7V3zlEx8trhF5PpvCX4jI/7OObRtXlcU3iu7FZnN6" crossorigin="anonymous">

    <link rel="stylesheet" href="sidebar/style.css">
</head>

<body>
    <div class="wrapper">
        <!-- sidebarframe -->
        <?php include "css/sidebar_admin.php" ?>
        <!-- end of sidebar frame -->
        <!-- main content -->
        <div class="main p-3">
            <div class="text-center">
                <h1>
                    Welcome, user
                </h1>
            </div>
            <!-- Add Exam Button - Button to trigger the first modal -->
            <div class="d-flex justify-content-center mt-5">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamModal">
                    Add Exam
                </button>
            </div>
            <!-- Exam Table -->
            <?php

            $result = mysqli_query(
                mysql: $conn,
                query: "SELECT q.title, q.total, q.eid, q.start_time, q.end_time, q.is_open, c.class_name 
                        FROM quiz q
                        JOIN class c ON c.class_id = q.class_id
                         WHERE c.lecturer_id = " . intval($lecturer_id)

            ) or die('Error');

            if (mysqli_num_rows($result) > 0) {
                echo '<div class="panel"><div class="table-responsive">
                    <table class="table table-striped title1">
                    <tr><td><center><b>S.N.</b></center></td>
                    <td><center><b>Topic</b></center></td>
                    <td><center><b>Total questions</b></center></td>
                    <td><center><b>Exam Id</b></center></td>
                    <td><center><b>Class</b></center></td>
                    <td><center><b>Question</b></center></td>
                    <td><center><b>Add question</b></center></td>
                    <td><center><b>Remove</b></center></td>
                    <td><center><b>Results</b></center></td>
                    <td><center><b>Status</b></center></td>
                    <td><center><b>Toggle</b></center></td>
                    <td><center><b>Closed time</b></center></td>
                    </tr>';
                $c = 1;
                while ($row = mysqli_fetch_array($result)) {
                    $topic = $row['title'];
                    $total = $row['total'];
                    $eid = $row['eid'];
                    $class = $row['class_name'];
                    $now = date('Y-m-d H:i:s');
                    $isOpen = $row['is_open'];
                    $start = $row['start_time'];
                    $end = $row['end_time'];

                    $examStatus = $row['is_open'] ? 'Open' : 'Closed';
                    echo '<tr><td><center>' . $c++ . '</center></td>
                    <td><center>' . $topic . '</center></td>
                    <td><center>' . $total . '</center></td>
                    <td><center>' . $eid . '</center></td>
                    <td><center>' . $class . '</center></td>
                    
                    <td><center>
                        <b><a href="exam_details.php?eid=' . $eid . '" justify-content:center class="btn sub1" style="margin:0px;background:grey;color:white">
                        <span class="glyphicon glyphicon-list" aria-hidden="true"></span>&nbsp;
                        <span class="title1"><b>Details</b></span></a></b></center>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-success addExamQuestionBtn" data-eid="' . $eid . '" data-bs-toggle="modal" data-bs-target="#addExamQuestionModal">
                              Add
                    </button>
                    </td>
                    <td><center><b><a href="/exam_app_upgraded/controller/add_exam.php?eid=' . $eid . '" class="justify-center btn sub1" 
                        style="margin:0px;background:red;color:black"><span class="glyphicon glyphicon-trash" 
                        aria-hidden="true"></span>&nbsp;<span class="title1"><b>Remove</b></span></a></b></center></td>
                    </td>
                    <td>
                        <center>
                        <a href="result.php?eid=' . $eid . '" class="btn btn-result">
                        🏆 &nbsp;
                        <span class="title1"><b>Result</b></span>
                        </a>
                        </center>
                    </td>
                    <td><center>
                        <span style="color:' . ($isOpen ? 'green' : 'red') . '; font-weight: bold;">
                         ' . $examStatus . '
                        </span>
                    </center></td>
                    <td><center>
                    <button class="btn btn-warning toggleExamStatusBtn" data-eid=' . $eid . '" data-bs-toggle="modal" data-bs-target="#toggleExamStatusModal">
                        Status
                    </button>
                    </center>
                    </td>
                    <td><center>' . $end . '</center></td>

                </tr>';
                }
                $c = 0;
                echo '</table></div></div>';
            } else {
                echo '<div class="alert alert-info text-center" role="alert">
            <strong>No exams available for your assigned classes.</strong>
          </div>';
            }
            ?>
        </div>
        <!--Modal to add exam-->
        <div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addExamModalLabel">Add New Exam</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="examForm" method="POST" action="/exam_app_upgraded/controller/add_exam.php">
                            <div class="mb-3">
                                <label for="title" class="form-label">Exam Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="exam_time" class="form-label">Exam Duration (input hours and
                                    minutes)</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control" id="exam_hours" name="exam_hours"
                                        placeholder="Hours" min="0" value="0" required>
                                    <input type="number" class="form-control" id="exam_minutes" name="exam_minutes"
                                        placeholder="Minutes" min="0" max="59" value="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="class_id" class="form-label">Select Class</label>
                                <select class="form-control" id="class_id" name="class_id" required>
                                    <option value="" disabled selected>Choose class</option>
                                    <?php foreach ($classOptions as $class): ?>
                                        <option value="<?= $class['class_id'] ?>">
                                            <?= htmlspecialchars($class['class_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal to add question -->
        <div class="modal fade" id="addExamQuestionModal" tabindex="-1" aria-labelledby="addExamQuestionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addExamQuestionModalLabel">Add Exam Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Choose how you want to add a question:</p>
                        <!-- Option to Add from Question Bank -->
                        <button class="btn btn-primary" id="redirectBtn">Add From Question Bank</button>
                        <!-- Direct Add question -->
                        <button class="btn btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#directlyAddQuestionModal" data-bs-dismiss="modal">
                            Directly Add Question
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add question from bank handler, see js-->


        <!-- 3rd Modal: Directly Add Question -->
        <div class="modal fade" id="directlyAddQuestionModal" tabindex="-1"
            aria-labelledby="directlyAddQuestionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="directlyAddQuestionModalLabel">Directly Add Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="directAddForm" method="POST"
                            action="/exam_app_upgraded/controller/directly_add_question.php">
                            <!-- Hidden field for storing `eid` -->
                            <input type="hidden" id="examId" name="examId">

                            <!-- Question Title -->
                            <div class="mb-3">
                                <label for="questionTitle" class="form-label">Question Title</label>
                                <input type="text" class="form-control" id="questionTitle" name="questionTitle"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="score" class="form-label">Score</label>
                                <input type="number" class="form-control" id="score" name="score" value="1" min="1"
                                    required>
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

                            <!-- Choices -->
                            <div id="choicesContainer"></div>

                            <!-- Correct Answer -->
                            <div class="mb-3" id="correctAnswerContainer" style="display: none;">
                                <label for="correctAnswer" class="form-label">Correct Answer</label>
                                <input type="text" class="form-control" id="correctAnswer" name="correctAnswer"
                                    placeholder="Enter the correct answer (e.g., A, B, C)" required>
                            </div>

                            <!-- Question Type -->
                            <div class="mb-3">
                                <label for="questionType" class="form-label">Question Type</label>
                                <input type="text" class="form-control" id="questionType" name="questionType" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Add Question</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            const numberOfChoices = document.getElementById('numberOfChoices');
            const choicesContainer = document.getElementById('choicesContainer');
            const correctAnswerContainer = document.getElementById('correctAnswerContainer');
            const correctAnswerInput = document.getElementById('correctAnswer');

            numberOfChoices.addEventListener('change', function () {
                const numChoices = parseInt(this.value, 10);
                choicesContainer.innerHTML = ''; // Clear previous fields

                if (!isNaN(numChoices)) {
                    // Generate input fields for choices
                    for (let i = 0; i < numChoices; i++) {
                        const choiceLetter = String.fromCharCode(65 + i); // A, B, C, ...
                        const choiceDiv = document.createElement('div');
                        choiceDiv.className = 'mb-3';
                        choiceDiv.innerHTML = `
                <label for="choice${choiceLetter}" class="form-label">Choice ${choiceLetter}</label>
                <input type="text" class="form-control" id="choice${choiceLetter}" name="choices[]" placeholder="Enter choice ${choiceLetter}" required>
            `;
                        choicesContainer.appendChild(choiceDiv);
                    }

                    // Show correct answer field
                    correctAnswerContainer.style.display = 'block';
                    correctAnswerInput.placeholder = `Enter the correct answer (e.g., A-${String.fromCharCode(65 + numChoices - 1)})`;
                } else {
                    correctAnswerContainer.style.display = 'none';
                }
            });
        </script>
    </div>
    </div>

    <!-- Toggle Exam Status Modal -->
    <div class="modal fade" id="toggleExamStatusModal" tabindex="-1" role="dialog"
        aria-labelledby="toggleExamStatusLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="toggleExamStatusForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Toggle Exam Status</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="eid" id="toggleExamEid">

                        <label><input type="radio" name="mode" value="permanent" checked> Toggle permanently</label><br>
                        <label><input type="radio" name="mode" value="range"> Toggle for a time range</label>

                        <div id="timeRangeFields" style="display: none; margin-top: 15px;">
                            <label>Start Time:</label>
                            <input type="datetime-local" name="start_time" class="form-control">
                            <label>End Time:</label>
                            <input type="datetime-local" name="end_time" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--handler for toggle exam status is in js -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
    <script src="js/addQuestionfromBank.js"></script>
    <script src="js/directlyAddQuestion.js"></script>
    <script src="js/toggleExamStatus.js"></script>
</body>

</html>

<style>
    .btn-result {
        background-color: #5a6268;
        /* better gray */
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 14px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-result:hover {
        background-color: #4e555b;
        /* darker on hover */
        transform: scale(1.05);
        /* slight zoom on hover */
        text-decoration: none;
        /* remove underline */
    }
</style>