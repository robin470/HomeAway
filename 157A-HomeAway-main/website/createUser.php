<?php
session_start();
include "database.php"; // Ensure database.php initializes $conn properly

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['phonenumber'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['username']);
    $pass = validate($_POST['password']);
    $firstName = validate($_POST['firstname']);
    $lastName = validate($_POST['lastname']);
    $email = validate($_POST['email']);
    $phoneNum = validate($_POST['phonenumber']);


    if (empty($uname)) {
        header("Location: index.php?error=ALL Values are required");
        exit();
        
    } else {
        // Use prepared statements for security
        $stmt = $conn->prepare("INSERT INTO user(userID, first_name, last_name, email, phone_number, password) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $uname, $firstName, $lastName, $email, $phoneNum, $pass); // create username and password
        $stmt->execute();
        $stmt->close();

                $_SESSION['userID'] = $uname;
                $_SESSION['id'] = $pass;
  
                header("Location: /website/home.php");
                exit();

    }


} else {
    header("Location: signup.php");
    exit();
}
?>