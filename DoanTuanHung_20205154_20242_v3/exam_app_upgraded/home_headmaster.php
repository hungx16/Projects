<?php
session_start();
include_once 'db_conn.php';
if (isset($_POST['add_class']) && !empty($_POST['class_name'])) {
    $class_name = trim($_POST['class_name']);
    $stmt = $conn->prepare("INSERT INTO class (class_name) VALUES (?)");
    $stmt->bind_param("s", $class_name);
    $stmt->execute();
    header("Location: home_headmaster.php");
    exit();
}

// Handle Delete Class
if (isset($_GET['delete_class'])) {
    $class_id = intval($_GET['delete_class']);
    $stmt = $conn->prepare("DELETE FROM class WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    header("Location: home_headmaster.php");
    exit();
}
// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'headmaster') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php?role=headmaster");
    exit();
}

// Optional: Additional session checks
if (!isset($_SESSION['user_name']) || !isset($_SESSION['id'])) {
    $_SESSION['error'] = "Session expired. Please log in again.";
    header("Location: index.php?role=headmaster");
    exit();
}

$name = $_SESSION['user_name'];

$query = "
    SELECT *
    FROM class c   
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
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
        <?php include "css/sidebar_headmaster.php" ?>
        <!-- end of sidebar frame -->
        <!-- main content -->
        <div class="main p-3">
            <div class="text-center">
                <h1>Admin panel</h1>
                <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
                <div class="d-flex justify-content-center mt-5">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addClassModal">
                        Add Class
                    </button>
                </div>
            </div>
            <?php
            echo '<div class="panel title"><table class="table table-striped title1">
            <tr style="color:black;">
            <td><center><b>Id</b></center></td>
            <td><center><b>Class name</b></center></td>
            <td><center><b>Lecturer Id</b></center></td>
            <td><center><b>Lecturer name</b></center></td>  
            <td><center><b>Assign</b></center></td> 
            <td><center><b>Delete lecturer</b></center></td> 
            <td><center><b>Delete class</b></center></td>
            </tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                <td><center>' . $row['class_id'] . '</center></td>
                <td><center>' . htmlspecialchars($row['class_name']) . '</center></td>
                <td><center>' . $row['lecturer_id'] . '</center></td>
                <td><center>' . $row['lecturer_name'] . '</center></td> 
                <td><center>
                <a href="#" class="btn btn-primary btn-sm assign-btn" data-class-id="' . $row['class_id'] . '" data-bs-toggle="modal" data-bs-target="#assignModal">Assign</a>
        </center></td>

        <td><center>';

                if (!empty($row['lecturer_name'])) {
                    // Form that submits to controller
                    echo '<form action="controller/assign_lecturer.php" method="POST" style="display:inline;">
                <input type="hidden" name="class_id" value="' . $row['class_id'] . '">
                <input type="hidden" name="user_id" value="' . $row['lecturer_id'] . '">
                <button type="submit" name="delete" class="btn btn-warning btn-sm"
                    onclick="return confirm(\'Remove lecturer from this class?\');">
                    Remove
                </button>
              </form>';
                } else {
                    echo '—'; // Empty slot if no lecturer assigned
                }

                echo '</center></td>

        <td><center>
                <a href="home_headmaster.php?delete_class=' . $row['class_id'] . '" 
           onclick="return confirm(\'Are you sure?\');" 
           class="btn btn-danger btn-sm">Delete</a>
    </center></td>

                </tr>';
            }

            echo '</table></div>';
            ?>
            <!-- Section 2: modal section -->
        </div>

    </div>
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="home_headmaster.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="class_name" class="form-label">Class Name</label>
                        <input type="text" name="class_name" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_class" class="btn btn-primary">Add Class</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Assign Lecturer Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="/exam_app_upgraded/controller/assign_lecturer.php" method="POST">
                <input type="hidden" name="class_id" id="modal_class_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignModalLabel">Assign Lecturer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Real Name</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $lecturers = mysqli_query($conn, "SELECT * FROM users WHERE role='lecturer'");
                                while ($lect = mysqli_fetch_assoc($lecturers)) {
                                    echo '<tr>
                          <td>' . $lect['id'] . '</td>
                          <td>' . htmlspecialchars($lect['user_name']) . '</td>
                          <td>' . htmlspecialchars($lect['real_name']) . '</td>
                          <td><input type="radio" name="user_id" value="' . $lect['id'] . '" required></td>
                        </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="assign" class="btn btn-success">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.assign-btn').forEach(button => {
            button.addEventListener('click', function () {
                const classId = this.getAttribute('data-class-id');
                document.getElementById('modal_class_id').value = classId;
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>