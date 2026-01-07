<?php
// sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_conn.php'; // adjust path as needed

$sql_count = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt_count = mysqli_prepare($conn, $sql_count);
mysqli_stmt_bind_param($stmt_count, "i", $user_id);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$unread_count = $row_count['unread_count'];
?>

<aside id="sidebar">
    <div class="d-flex">
        <button class="toggle-btn" type="button">
            <i class="lni lni-grid-alt"></i>
        </button>
        <div class="sidebar-logo">
            <a href="home.php">Exam</a>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="home.php" class="sidebar-link" data-target="home">
                <i class="lni lni-home"></i>
                <span>Home</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="history.php" class="sidebar-link" data-target="user">
                <i class="lni lni-user"></i>
                <span>History</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="ranking.php" class="sidebar-link" data-target="result">
                <i class="lni lni-bar-chart"></i>
                <span>Ranking</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="notification.php" class="sidebar-link">
                <i class="lni lni-alarm"></i>
                <span>Notification</span>
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <div class="sidebar-footer">
            <a href="logout.php" class="sidebar-link">
                <i class="lni lni-exit"></i>
                <span>Logout</span>
            </a>
        </div>

</aside>