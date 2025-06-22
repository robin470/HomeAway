<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="container">
        <div class="left-section">
            <img src="pic/login.png" alt="Logo" class="logo">
        </div>

        <form action="createUser.php" method="post">
            <h2>Sign Up!</h2>
            <?php if (isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <label>User Name</label>
            <input type="text" name="username" placeholder="User Name"><br>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>

            <label>First Name</label>
            <input type="text" name="firstname" placeholder="First Name"><br>
          
            <label>Last Name</label>
            <input type="text" name="lastname" placeholder="Last Name"><br>
         
            <label>Email</label>
            <input type="text" name="email" placeholder="User Name"><br>
            <label>Phone Number</label>
            <input type="text" name="phonenumber" placeholder="User Name"><br>
            
            
            <button type="submit">Sign Up!</button>

            <p class="create-account">
                <a href="/website/index.php">Have an Account? Login</a>
            </p>

        </form>
    </div>

</body>

</html>