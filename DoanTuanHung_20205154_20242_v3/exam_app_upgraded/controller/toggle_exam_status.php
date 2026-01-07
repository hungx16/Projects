<?php
include 'db_conn.php';

$eid = $_POST['eid'];
$mode = $_POST['mode'];

if ($mode === 'permanent') {
    // Toggle and clear time
    $result = mysqli_query($conn, "SELECT is_open FROM quiz WHERE eid='$eid'");
    $row = mysqli_fetch_assoc($result);
    $new_state = $row['is_open'] == 1 ? 0 : 1;

    mysqli_query($conn, "
        UPDATE quiz SET 
            is_open = $new_state,
            start_time = NULL,
            end_time = NULL
        WHERE eid = '$eid'
    ");
    echo "Exam status permanently set to " . ($new_state ? 'Open' : 'Closed');
} else {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if (!$start_time || !$end_time) {
        echo "Start and end time required.";
        exit;
    }

    mysqli_query($conn, "
        UPDATE quiz SET 
            start_time = '$start_time',
            end_time = '$end_time'
        WHERE eid = '$eid'
    ");

    // is_open will be updated dynamically on page load via PHP time logic
    echo "Exam will open from $start_time to $end_time.";
}
?>