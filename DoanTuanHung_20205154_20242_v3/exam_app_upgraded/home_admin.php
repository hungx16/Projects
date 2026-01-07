<?php
session_start();
include_once 'db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php?role=lecturer");
    exit();
}

// Optional: Additional session checks
if (!isset($_SESSION['user_name']) || !isset($_SESSION['id'])) {
    $_SESSION['error'] = "Session expired. Please log in again.";
    header("Location: index.php?role=lecturer");
    exit();
}
$lecturer_user = $_SESSION['user_name'];

$query = "
    SELECT c.class_id, c.class_name, COUNT(uc.user_id) AS student_count
    FROM class c
    JOIN users u ON c.lecturer_name = u.real_name
    LEFT JOIN users_class uc ON c.class_id = uc.class_id
    WHERE u.user_name = ?
    GROUP BY c.class_id, c.class_name
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $lecturer_user);
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
    <!-- Alternative Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

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
                <h1>Welcome, <?php echo htmlspecialchars($lecturer_user); ?></h1>
            </div>
            <!-- Button to trigger modal -->
            <div style="display: flex; justify-content: center; margin-bottom: 10px;">
                <button onclick="showAddUserModal()" class="btn btn-primary">Add Student</button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <div class="mb-3">
                                    <label for="user_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="class_name" class="form-label">Class</label>
                                    <input type="text" class="form-control" id="class_name" name="class_name" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Student</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function showAddUserModal() {
                    var modal = new bootstrap.Modal(document.getElementById('addUserModal'));
                    modal.show();
                }

                document.getElementById("addUserForm").addEventListener("submit", function (event) {
                    event.preventDefault(); // Prevent page reload

                    let formData = new FormData(this);

                    fetch("/exam_app_upgraded/controller/add_user.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("User successfully added!");
                                location.reload(); // Refresh page to reflect changes
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
                function removeUserFromClass(userName, className) {
                    if (!confirm("Are you sure you want to remove this user from the class?")) return;

                    let formData = new FormData();
                    formData.append("user_name", userName);
                    formData.append("class_name", className);
                    formData.append("action", "delete");

                    fetch("/exam_app_upgraded/controller/add_user.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            if (data.success) location.reload();
                        });
                }
            </script>

            <!-- Button to trigger bulk upload modal -->
            <div style="display: flex; justify-content: center; margin-bottom: 10px;">
                <button onclick="showBulkAddModal()" class="btn btn-secondary">Add Multiple Students</button>
            </div>

            <!-- Modal for bulk add -->
            <div class="modal fade" id="bulkAddModal" tabindex="-1" aria-labelledby="bulkAddModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkAddModalLabel">Add Multiple Students via CSV</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="bulkAddForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="csvFile" class="form-label">Upload CSV File</label>
                                    <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv"
                                        required>
                                    <div class="form-text">Expected CSV columns:
                                        <strong>user_name,class_name</strong>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload and Add Users</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function showBulkAddModal() {
                    const modal = new bootstrap.Modal(document.getElementById('bulkAddModal'));
                    modal.show();
                }

                document.getElementById("bulkAddForm").addEventListener("submit", function (event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    fetch("/exam_app_upgraded/controller/bulk_add_users.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error("Upload failed:", error);
                            alert("Failed to upload file.");
                        });
                });
            </script>
            <?php

            echo '<div class="panel title"><table class="table table-striped title1">
        <tr style="color:black;">
            <td><center><b>Id</b></center></td>
            <td><center><b>Class name</b></center></td>
            <td><center><b>Students No.</b></center></td>
            <td><center><b>Details</b></center></td>
        </tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                <td><center>' . $row['class_id'] . '</center></td>
                <td><center>' . htmlspecialchars($row['class_name']) . '</center></td>
                <td><center>' . $row['student_count'] . '</center></td>
                <td><center><a href="user.php?class_id=' . $row['class_id'] . '" class="btn btn-sm btn-primary">Details</a></center></td>
            </tr>';
            }

            echo '</table></div>';
            ?>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>

</html>