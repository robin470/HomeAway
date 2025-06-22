<!DOCTYPE html>
<html>

<head>
    <title>LOGIN</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="container">
        <div class="left-section">
            <img src="pic/login.png" alt="Logo" class="logo">
        </div>

        <form action="login.php" method="post">
            <h2>Welcome Back!</h2>
            <?php if (isset($_GET['error'])) { ?>
                <p class="error"><?php echo $_GET['error']; ?></p>
            <?php } ?>
            <label>User Name</label>
            <input type="text" name="uname" placeholder="User Name"><br>

            <label>Password</label>
            <input type="password" name="password" placeholder="Password"><br>
            <button type="submit">Login</button>

            <p class="create-account">
                <a href="/website/signup.php">First time? Create an account</a>
            </p>

        </form>
    </div>

</body>

</html>