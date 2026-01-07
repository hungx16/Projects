<?php
session_start();
include "db_conn.php";

if (
	isset($_POST['uname']) && isset($_POST['password'])
	&& isset($_POST['name']) && isset($_POST['re_password'])
	&& isset($_POST['role'])
) {

	function validate($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$uname = validate($_POST['uname']);
	$pass = validate($_POST['password']);
	$re_pass = validate($_POST['re_password']);
	$name = validate($_POST['name']);
	$role = validate($_POST['role']); // Get the role

	$user_data = 'uname=' . $uname . '&name=' . $name . '&role=' . $role;

	if (empty($uname)) {
		header("Location: /exam_app_upgraded/signup.php?error=User Name is required&$user_data");
		exit();
	} else if (empty($pass)) {
		header("Location: /exam_app_upgraded/signup.php?error=Password is required&$user_data");
		exit();
	} else if (empty($re_pass)) {
		header("Location: /exam_app_upgraded/signup.php?error=Re Password is required&$user_data");
		exit();
	} else if (empty($name)) {
		header("Location: /exam_app_upgraded/signup.php?error=Name is required&$user_data");
		exit();
	} else if (empty($role)) {
		header("Location: /exam_app_upgraded/signup.php?error=Role is required&$user_data");
		exit();
	} else if ($pass !== $re_pass) {
		header("Location: /exam_app_upgraded/signup.php?error=The confirmation password does not match&$user_data");
		exit();
	} else {
		// Hash the password
		$pass = md5($pass);

		$sql = "SELECT * FROM users WHERE user_name='$uname'";
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
			header("Location: /exam_app_upgraded/signup.php?error=The username is taken try another&$user_data");
			exit();
		} else {
			$sql2 = "INSERT INTO users(user_name, password, real_name, role) 
                     VALUES('$uname', '$pass', '$name', '$role')";
			$result2 = mysqli_query($conn, $sql2);

			if ($result2) {
				header("Location: /exam_app_upgraded/signup.php?success=Your account has been created successfully");
				exit();
			} else {
				header("Location: /exam_app_upgraded/signup.php?error=Unknown error occurred&$user_data");
				exit();
			}
		}
	}

} else {
	header("Location: /exam_app_upgraded/signup.php");
	exit();
}
