<?php
session_start();
include "db_conn.php";

// ✅ First, define the function at the top
function validate($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

if (isset($_POST['uname']) && isset($_POST['password'])) {

	$role = validate($_POST['role']);
	$uname = validate($_POST['uname']);
	$pass = validate($_POST['password']);

	if (empty($uname)) {
		$_SESSION['error'] = "User Name is required";
		header("Location: index.php?role=$role");
		exit();
	} else if (empty($pass)) {
		$_SESSION['error'] = "Password is required";
		header("Location: index.php?role=$role");
		exit();
	} else {
		// hashing the password
		$pass = md5($pass);

		$sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if ($row['user_name'] === $uname && $row['password'] === $pass) {

				if ($row['role'] !== $role) {
					echo "<script>
						alert('Unauthorized access!');
						window.location.href = 'index.php';
					</script>";
					exit();
				}

				$_SESSION['user_name'] = $row['user_name'];
				$_SESSION['name'] = $row['name'];
				$_SESSION['id'] = $row['id'];
				$_SESSION['role'] = $row['role']; // Save role

				if ($row['role'] === 'lecturer') {
					header("Location: home_admin.php");
				} else if ($row['role'] === 'student') {
					header("Location: home.php");
				} else {
					header("Location: home_headmaster.php");
				}
				exit();
			} else {
				$_SESSION['error'] = "Incorrect username or password";
				header("Location: index.php?role=$role");
				exit();
			}
		} else {
			$_SESSION['error'] = "Incorrect username or password";
			header("Location: index.php?role=$role");
			exit();
		}
	}

} else {
	header("Location: index.php");
	exit();
}
