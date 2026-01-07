<!DOCTYPE html>
<html>

<head>
     <title>SIGN UP</title>
     <link rel="stylesheet" type="text/css" href="css/sign_up.css">
</head>

<body>
     <div class="signup-container">
          <form action="/exam_app_upgraded/controller/signup-check.php" method="post">
               <h2>SIGN UP</h2>
               <?php if (isset($_GET['role'])): ?>
                    <h3>Signing up as: <strong><?php echo htmlspecialchars($_GET['role']); ?></strong></h3>
               <?php endif; ?>

               <!-- Hidden role field -->
               <input type="hidden" name="role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">
               <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
               <?php } ?>

               <?php if (isset($_GET['success'])) { ?>
                    <p class="success"><?php echo $_GET['success']; ?></p>
               <?php } ?>

               <label>Name</label>
               <?php if (isset($_GET['name'])) { ?>
                    <input type="text" name="name" placeholder="Name" value="<?php echo $_GET['name']; ?>"><br>
               <?php } else { ?>
                    <input type="text" name="name" placeholder="Name"><br>
               <?php } ?>

               <label>User Name</label>
               <?php if (isset($_GET['uname'])) { ?>
                    <input type="text" name="uname" placeholder="User Name" value="<?php echo $_GET['uname']; ?>"><br>
               <?php } else { ?>
                    <input type="text" name="uname" placeholder="User Name"><br>
               <?php } ?>


               <label>Password</label>
               <input type="password" name="password" placeholder="Password"><br>

               <label>Re Password</label>
               <input type="password" name="re_password" placeholder="Re_Password"><br>

               <button type="submit">Sign Up</button>
               <a href="index.php" class="ca">Already have an account?</a>
          </form>
     </div>
</body>

</html>