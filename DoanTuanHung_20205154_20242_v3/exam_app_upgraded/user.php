<?php
include_once 'db_conn.php';
session_start();
if (!(isset($_SESSION['user_name']))) {
    header("location:login.php");
} else {
    $name = $_SESSION['name'];

    include_once 'db_conn.php';
}
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
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
        <?php include "css/sidebar_admin.php" ?>
        <!-- end of sidebar frame -->
        <div class="main p-3">
            <!-- main content -->
            <div class="text-center">
                <h2>User Management</h2>
            </div>
            <div class="text-center">
                <h1>
                    Welcome, user
                </h1>

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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addUserForm">
                                    <div class="mb-3">
                                        <label for="user_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="user_name" name="user_name"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_name" class="form-label">Class</label>
                                        <input type="text" class="form-control" id="class_name" name="class_name"
                                            required>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="bulkAddForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="csvFile" class="form-label">Upload CSV File</label>
                                        <input type="file" class="form-control" id="csvFile" name="csvFile"
                                            accept=".csv" required>
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
                $result = mysqli_query(
                    mysql: $conn,
                    query: "SELECT u.user_name, c.class_name 
     FROM users u
     JOIN users_class uc ON u.id = uc.user_id
     JOIN class c ON uc.class_id = c.class_id
     WHERE c.class_id = $class_id;
                        "
                ) or die('Error');
                echo '<div class="panel"><div class="table-responsive">
                        <table class="table table-striped title1">
                        <tr><td><center><b>S.N.</b></center></td>
                        <td><center><b>Name</b></center></td>
                        <td><center><b>Class</b></center></td>
                        <td><center><b>Remove user</b></center></td>
                        </tr>';
                $c = 1;
                while ($row = mysqli_fetch_array($result)) {
                    $name = $row['user_name'];
                    $class = $row['class_name'];
                    echo '<tr><td><center>' . $c++ . '</center></td>
                        <td><center>' . $name . '</center></td>
                        <td><center>' . $class . '</center></td>
                        <td><center><b><a href="#" onclick="removeUserFromClass(\'' . $name . '\', \'' . $class . '\')" class="justify-center btn sub1" style="margin:0px;background:red;color:black">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>&nbsp;<span class="title1"><b>Remove</b></span></a></b></center></td></tr>';
                }
                $c = 0;
                echo '</table></div></div>';
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