<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['user_name'];

    include_once 'db_conn.php';
}
$eid = isset($_GET['eid']) ? trim($_GET['eid']) : '';
$examTitle = isset($_GET['examTitle']) ? trim($_GET['examTitle']) : '';
$className = isset($_GET['className']) ? trim($_GET['className']) : '';

$query = "SELECT 
            r.user_name, 
            u.real_name AS full_name,
            r.score, 
            r.time, 
            r.title, 
            r.class_name, 
            r.eid 
          FROM rank r
          JOIN users u ON r.user_name = u.user_name
          WHERE 1";

if (!empty($eid)) {
    $query .= " AND r.eid = '" . mysqli_real_escape_string($conn, $eid) . "'";
    $hasResults = true;
} else {
    if (!empty($examTitle)) {
        $query .= " AND LOWER(r.title) LIKE '%" . strtolower(mysqli_real_escape_string($conn, $examTitle)) . "%'";
        $hasResults = true;
    }
}

if (!empty($className)) {
    $query .= " AND LOWER(r.class_name) LIKE '%" . strtolower(mysqli_real_escape_string($conn, $className)) . "%'";
    $hasResults = true;
}

$query .= " ORDER BY r.score DESC, r.time ASC";
$result = $conn->query($query) or die('Error');
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
        <!-- sidebarframe -->
        <?php include "css/sidebar_admin.php" ?>
        <!-- end of sidebar frame -->
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h1>
                    <h1>Result of exam <?php echo htmlspecialchars($eid); ?></h1>
                </h1>
            </div>
            <div class="text-center">
                <div class="container">
                    <!-- Filter Form -->

                    <div id="rankingTable" class="table-responsive">
                        <table class="table table-striped title1">
                            <tr style="color:red">
                                <th>
                                    <center>Number</center>
                                </th>
                                <th>
                                    <center>Name</center>
                                </th>
                                <th>
                                    <center>Full name</center>
                                </th>
                                <th>
                                    <center>Score</center>
                                </th>
                                <th>
                                    <center>Time</center>
                                </th>
                                <th>
                                    <center>Exam Title</center>
                                </th>
                                <th>
                                    <center>Class</center>
                                </th>
                                <th>
                                    <center>Details</center>
                                </th>
                            </tr>

                            <?php
                            $rank = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                $name = htmlspecialchars($row['user_name']);
                                $eid = urlencode($row['eid']);
                                $score = htmlspecialchars($row['score']);
                                $time = htmlspecialchars($row['time']);
                                $title = htmlspecialchars($row['title']);
                                $class_name = htmlspecialchars($row['class_name']);
                                ?>
                                <tr>
                                    <td>
                                        <center><?= $rank ?></center>
                                    </td>
                                    <td>
                                        <center><?= $name ?></center>
                                    </td>
                                    <td>
                                        <center><?= htmlspecialchars($row['full_name']) ?></center>
                                    </td>
                                    <td>
                                        <center><?= $score ?></center>
                                    </td>
                                    <td>
                                        <center><?= $time ?></center>
                                    </td>
                                    <td>
                                        <center><?= $title ?></center>
                                    </td>
                                    <td>
                                        <center><?= $class_name ?></center>
                                    </td>
                                    <td>
                                        <center>
                                            <a href="history_details.php?eid=<?= urlencode($row['eid']) ?>&username=<?= urlencode($row['user_name']) ?>"
                                                class="btn sub1" style="color:black;margin:0px;background:#1de9b6">
                                                <span class="glyphicon glyphicon-list" aria-hidden="true"></span>&nbsp;<span
                                                    class="title1"><b>Details</b></span>
                                            </a>
                                        </center>
                                    </td>
                                </tr>
                                <?php
                                $rank++;
                            endwhile;
                            ?>
                        </table>
                    </div>
                </div>

            </div>
            <!-- end of main content -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>