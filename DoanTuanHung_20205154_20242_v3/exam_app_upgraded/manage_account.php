<?php
session_start();
include_once 'db_conn.php';

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
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);

    // Optional: Add a check to prevent deleting the headmaster themselves
    // Example: if ($user_id === $_SESSION['user_id']) { exit("Can't delete yourself"); }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: manage_account.php");
    exit();
}
$name = $_SESSION['user_name'];


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

            </div>


            <?php
            include 'db_conn.php'; // ensure $conn is set up properly
            
            echo '<div class="panel title"><table class="table table-striped title1">
<tr style="color:black;">
    <td><center><b>Id</b></center></td>
    <td><center><b>Username</b></center></td>
    <td><center><b>Real Name</b></center></td>
    <td><center><b>Role</b></center></td>
    <td><center><b>Delete Account</b></center></td>
</tr>';

            $query = "SELECT id, user_name, real_name, role FROM users 
                WHERE role IN ('student', 'lecturer')";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
    <td><center>' . $row['id'] . '</center></td>
    <td><center>' . htmlspecialchars($row['user_name']) . '</center></td>
    <td><center>' . htmlspecialchars($row['real_name']) . '</center></td>
    <td><center>' . htmlspecialchars($row['role']) . '</center></td>
    <td><center>
        <a href="manage_account.php?delete_user=' . $row['id'] . '" 
           onclick="return confirm(\'Are you sure you want to delete this account?\');" 
           class="btn btn-danger btn-sm">Delete</a>
    </center></td>
    </tr>';
            }

            echo '</table></div>';

            // Handle account deletion
            
            ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>