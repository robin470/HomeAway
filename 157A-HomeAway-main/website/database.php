<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "rootp";
$dbname = "HOMEAWAYDB";
$conn = "";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
} catch (mysqli_sql_exception) {
    echo "Could not connect.";
}


if ($conn) {
 
}
