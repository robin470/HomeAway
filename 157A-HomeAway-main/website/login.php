<?php
session_start();
include "database.php"; // Ensure database.php initializes $conn properly

if (isset($_POST['uname']) && isset($_POST['password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    if (empty($uname)) {
        header("Location: index.php?error=User Name is required");
        exit();
    } else if (empty($pass)) {
        header("Location: index.php?error=Password is required");
        exit();
    } else {
        // Use prepared statements for security
        $stmt = $conn->prepare("SELECT * FROM user WHERE userID = ? AND password = ?");
        $stmt->bind_param("ss", $uname, $pass); // Bind username and password
        $stmt->execute();
        $result = $stmt->get_result();

        

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($row['userID'] === $uname && $row['password'] === $pass) {
                $_SESSION['userID'] = $row['userID'];
                $_SESSION['id'] = $row['password'];
  
                header("Location: /website/home.php");
                exit();
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password");
            exit();
        }
    }
} else {
    header("Location: index.php");
    exit();
}
?>