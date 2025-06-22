<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "rootp";
$dbname = "company";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the database
$sql = "SELECT Pname, Pnumber, Plocation FROM project";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Results</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<h2>User List</h2>

<table>
    <tr>
        <th>Pname</th>
        <th>ProjectNo</th>
        <th>Email</th>
    </tr>
    <?php
    // Check if there are any results and display them
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["Pname"] . "</td><td>" . $row["Pnumber"] . "</td><td>" . $row["Plocation"] . "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No results found</td></tr>";
    }
    $conn->close();
    ?>
</table>

</body>
</html>
