<?php
include_once 'db_conn.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header("location:login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    if (isset($_SESSION['user_name'])) {
        $user_name = $_SESSION['user_name'];

        // Get user_id again
        $sql_user = "SELECT id FROM users WHERE user_name = ?";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "s", $user_name);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);

        if ($row = mysqli_fetch_assoc($result_user)) {
            $user_id = $row['id'];

            // Delete notifications for this user
            $sql_delete = "DELETE FROM notifications WHERE user_id = ?";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $user_id);
            mysqli_stmt_execute($stmt_delete);
        }
    }

    // Refresh the page to reflect deletion
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$user_name = $_SESSION['user_name'];

// Step 1: Get user ID from `users` table
$sql_user = "SELECT id FROM users WHERE user_name = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "s", $user_name);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

if ($row = mysqli_fetch_assoc($result_user)) {
    $user_id = $row['id'];

    // Step 2: Get notifications for this user
    $sql_notif = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_notif = mysqli_prepare($conn, $sql_notif);
    mysqli_stmt_bind_param($stmt_notif, "i", $user_id);
    mysqli_stmt_execute($stmt_notif);
    $result_notif = mysqli_stmt_get_result($stmt_notif);

    $notifications = [];
    while ($notif = mysqli_fetch_assoc($result_notif)) {
        $notifications[] = $notif;
    }
} else {
    // User not found
    $notifications = [];
}
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
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'lecturer') {
            include "css/sidebar_admin.php";
        } else {
            include "css/sidebar.php";
        }
        ?>
        <div class="main p-3">
            <div class="container mt-4">
                <div class="text-center">
                    <h2>Your Notifications</h2>
                </div>
                <div class="text-center">
                    <form method="POST" onsubmit="return confirm('Delete all notifications?');">
                        <input type="hidden" name="delete_all" value="1">
                        <button type="submit" class="btn btn-danger mb-3">Delete All Notifications</button>
                    </form>
                </div>
                <ul class="list-group">
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $note): ?>
                            <li class="list-group-item <?php echo $note['is_read'] ? '' : 'fw-bold'; ?>">
                                <?php echo htmlspecialchars($note['message']); ?>
                                <br><small class="text-muted"><?php echo $note['created_at']; ?></small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">No notifications.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>