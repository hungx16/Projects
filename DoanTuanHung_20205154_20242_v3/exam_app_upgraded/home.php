<?php
include_once 'db_conn.php';
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php?role=lecturer");
    exit();
}

// Optional: Additional session checks
if (!isset($_SESSION['user_name']) || !isset($_SESSION['id'])) {
    $_SESSION['error'] = "Session expired. Please log in again.";
    header("Location: index.php?role=setudent");
    exit();
} else {
    $name = $_SESSION['user_name'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    include_once 'db_conn.php';
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
        <?php include "css/sidebar.php" ?>
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h1>
                    <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
                </h1>
            </div>
            <!-- print all exam and the take exam button -->
            <?php

            $result = mysqli_query(
                mysql: $conn,
                query: "SELECT q.title, q.total, q.eid, q.start_time, q.end_time, q.is_open, c.class_name 
                        FROM quiz q
                        JOIN class c ON c.class_id = q.class_id
                         JOIN users_class uc ON uc.class_id = c.class_id
                        WHERE uc.user_id = $user_id
                    "
            ) or die('Error');
            echo '<div class="panel"><div class="table-responsive">
                    <table class="table table-striped title1">
                    <tr><td><center><b>S.N.</b></center></td>
                    <td><center><b>Title</b></center></td>
                    <td><center><b>Total questions</b></center></td>
                    <td><center><b>Exam Id</b></center></td>
                    <td><center><b>Class</b></center></td>
                    <td><center><b>Status</b></center></td>
                    <td><center><b>Take Exam</b></center></td>
                    
                    </tr>';
            $c = 1;
            while ($row = mysqli_fetch_array($result)) {
                $topic = $row['title'];
                $total = $row['total'];
                $eid = $row['eid'];
                $class = $row['class_name'];
                $isOpen = $row['is_open'];
                $examStatus = $row['is_open'] ? 'Open' : 'Closed';
                echo '<tr><td><center>' . $c++ . '</center></td>
                    <td><center>' . $topic . '</center></td>
                    <td><center>' . $total . '</center></td>
                    <td><center>' . $eid . '</center></td>
                    <td><center>' . $class . '</center></td>
                    <td><center>
                        <span style="color:' . ($isOpen ? 'green' : 'red') . '; font-weight: bold;">
                         ' . $examStatus . '
                        </span>
                    </center></td>
                   <td><center>
                    ' . ($isOpen
                    ? '<a href="take_exam.php?eid=' . $eid . '" class="btn sub1" style="margin:0px;background:green;color:white">
                <span class="glyphicon glyphicon-list" aria-hidden="true"></span>&nbsp;
                <span class="title1"><b>Take</b></span>
            </a>'
                    : '<button class="btn sub1" style="margin:0px;background:grey;color:white" onclick="alert(\'Unable to take exam\')" disabled>
                <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>&nbsp;
                <span class="title1"><b>Take</b></span>
                     </button>'
                ) . '
                </center></td>
                        
                </tr>';
            }
            $c = 0;
            echo '</table></div></div>';

            ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>
<!-- end of frame -->