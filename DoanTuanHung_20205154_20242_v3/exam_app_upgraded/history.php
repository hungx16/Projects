<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['user_name'];

    include_once 'db_conn.php';
}
$query = "SELECT h.eid, q.title, h.total_questions, h.correct, h.wrong, h.score 
          FROM history h 
          JOIN quiz q ON h.eid = q.eid
          WHERE h.user_name = '$name'
          ORDER BY h.date DESC";

$result = mysqli_query($conn, $query);
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
    <link rel="stylesheet" href="sidebar/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include "css/sidebar.php" ?>
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h1>
                    <?php echo htmlspecialchars($name); ?>'s exam history
                </h1>
                <?php

                echo '<div class="panel title">
                    <table class="table table-striped title1">
                    <tr style="color:black;">
                    <td><center><b>S.N.</b></center></td>
                    <td><center><b>Quiz</b></center></td>
                    <td><center><b>Question Solved</b></center></td>
                    <td><center><b>Right</b></center></td>
                    <td><center><b>Wrong</b></center></td>
                    <td><center><b>Score</b></center></td>
                    <td><center><b>Details</b></center></td>
                    </tr>';

                $c = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    $eid = $row['eid'];
                    $title = $row['title'];
                    $solved = $row['total_questions'];
                    $r = $row['correct'];
                    $w = $row['wrong'];
                    $s = $row['score'];

                    echo '<tr>
                        <td><center>' . $c++ . '</center></td>
                        <td><center>' . $title . '</center></td>
                        <td><center>' . $solved . '</center></td>
                        <td><center>' . $r . '</center></td>
                        <td><center>' . $w . '</center></td>
                        <td><center>' . $s . '</center></td>
                        <td><center><b><a href="history_details.php?eid=' . $eid . ($_SESSION['role'] === 'lecturer' ? '&username=' . $row['user_name'] : '') . '">
                        <b>Details</b></span></a></b></center></td>
                        </tr>';
                }

                echo '</table></div>';
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>