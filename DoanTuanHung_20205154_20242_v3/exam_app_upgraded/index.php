<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>LOGIN</title>
    <link rel="stylesheet" href="css/role_box.css">
    <link rel="stylesheet" href="css/login.css">

    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <!-- Step 1: Choose role -->
    <div class="role-box">
        <h2>Who are you?</h2>
        <button onclick="openModal('student')">Student</button>
        <button onclick="openModal('lecturer')">Lecturer</button>
        <button onclick="openModal('headmaster')">Headmaster</button>
    </div>

    <!-- Modal -->
    <div class="login-overlay hidden" id="loginModal">
        <form class="login-box" action="login.php" method="post">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="loginTitle">LOGIN</h2>

            <!-- Display error message from session -->
            <?php if (isset($_SESSION['error'])) { ?>
                <p class="error"><?php echo $_SESSION['error']; ?></p>
                <?php unset($_SESSION['error']); // Remove error message after displaying ?>
            <?php } ?>

            <input type="hidden" name="role" id="roleInput" value="">

            <label>User Name</label>
            <input type="text" name="uname" placeholder="User Name"><br>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>

            <button type="submit">Login</button>
            <br><br>
            <a href="signup.php" class="ca">Create an account</a>
        </form>
    </div>

    <script>
        function openModal(role) {
            document.querySelector('.role-box').classList.add('hidden');
            const modal = document.getElementById('loginModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex'; // ensure flex alignment
            document.getElementById('roleInput').value = role;
            document.getElementById('loginTitle').textContent = `LOGIN as ${role.charAt(0).toUpperCase() + role.slice(1)}`;
            document.querySelector('.ca').href = `signup.php?role=${role}`;
        }

        function closeModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.querySelector('.role-box').classList.remove('hidden');
        }

        window.onclick = function (event) {
            const modal = document.getElementById('loginModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Automatically open the modal based on role in the query string
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            const role = urlParams.get('role');
            if (role) {
                openModal(role); // Open the modal for the role passed in the URL
            }
        }
    </script>

</body>

</html>