<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=HomeAwaydb;charset=utf8", 'root', 'rootp');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$propertyID = null;

// Check if propertyID exists in POST request or is available in session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['make_payment'])) {
    // Check for propertyID in POST or session
    if (isset($_POST['propertyID'])) {
        $propertyID = htmlspecialchars($_POST['propertyID']);
        $_SESSION['propertyID'] = $propertyID; // Store in session
    } elseif (isset($_SESSION['propertyID'])) {
        $propertyID = $_SESSION['propertyID']; // Use session value if POST is not available
    }


} else {
    echo "Form not submitted via POST.";
}


$stmtProperty = $pdo->prepare("SELECT * FROM Property WHERE propertyID = :propertyID");
$stmtProperty->execute(['propertyID' => $propertyID]);
$property = $stmtProperty->fetch(PDO::FETCH_ASSOC);



// Payment processing logic
if (isset($_POST['make_payment'])) {
    echo "working";

    // Ensure all fields are completed
    if (isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['payment_method'])) {
        // Store input values
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $paymentMethod = $_POST['payment_method'];
        $userID = $_SESSION['userID']; // Ensure userID is in session
        $propertyID = $_SESSION['propertyID']; // Use session propertyID
        // Ensure all fields are completed
        if ($startDate != "" && $endDate != "" && $paymentMethod != "") {
            try {
                // Insert booking into database
                $stmt = $pdo->prepare("
                    INSERT INTO Booking (reservation_start, reservation_end, userID, propertyID)
                    VALUES (:startDate, :endDate, :userID, :propertyID)
                ");
                $stmt->execute([
                    ':startDate' => $startDate,
                    ':endDate' => $endDate,
                    ':userID' => $userID,
                    ':propertyID' => $propertyID
                ]);

            


                // Redirect to profile page after successful booking
                header("Location: history.php");
                exit();
            } catch (Exception $e) {
                echo "Failed to book the property: " . $e->getMessage();
                exit();
            }
        }
    } else {
        echo "Please complete all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeAway</title>
    <link rel="stylesheet" href="css/cartStyle.css" />
</head>

<body>
    <!-- Header Section -->
    <header>
        <a href="home.php" class="logo">
            <h1><img src="pic/homeAwayText.png" alt="Logo"></h1>
        </a>
        <div class="search-bar">
            <form method="GET" action="">
                <input
                    type="text"
                    name="city"
                    placeholder="Location: San Jose CA">
                <button type="submit"><img src="pic/searchIcon.png" alt="Search"></button>
            </form>
        </div>
        <div class="header-icons">
            <a href="history.php">
                <img src="pic/historyIcon.png" alt="History">
            </a>
            <a href="profile.php">
                <img src="pic/profileIcon.png" alt="User Profile">
            </a>
            <a href="logout.php">
                <img src="pic/logoutIcon.png" alt="Log out">
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Property Name and Address -->
        <div class="section">
            <div class="title"><?php echo htmlspecialchars($property['type']); ?></div>
            <p><?php echo htmlspecialchars($property['street']); ?></p>
        </div>
        <!-- Property Details -->
        <div class="section">
            <div class="row">
                <!-- Details -->
                <div class="info_column">
                    <div class="detail">
                        <div class="section">
                            <div class="subheading">Details</div>
                            <label>Property Type:<?php echo htmlspecialchars($property['type']); ?></label>
                            <label>No. Bedrooms: <?php echo htmlspecialchars($property['num_bedrooms']); ?></label>
                            <label>No. Bathrooms:<?php echo htmlspecialchars($property['num_bathrooms']); ?></label>
                            <label>Max. Occupancy:<?php echo htmlspecialchars($property['max_guests']); ?></label>
                            <label>Pricing:<?php echo htmlspecialchars($property['price']); ?></label>
                        </div>
                    </div>
                </div>

                <!-- An Image -->
                <div class="photo">
                    <img src="pic/homeAwayLogo.png" alt="Logo">
                </div>
            </div>
        </div>
        <!-- Payment form -->
        <form class="section" action="" method="post">
            <div class="subheading">Payment</div>
            <div class="row">
                <!-- Left side of form (Reservation Time) -->
                <div class="detail">
                    <div class="label_column">
                        <label>Start Date:</label>
                        <label>End Date:</label>
                    </div>
                    <div class="input_column">
                        <input class="text" type="date" id="start_date" name="start_date">
                        <input class="text" type="date" id="end_date" name="end_date">
                    </div>
                </div>
                <!-- Right side of form (Payment Details) -->
                
                <div class="detail">
                    <div class="label_column">
                        <label> </label>
                        <label>Payment Method:</label>
                    </div>


                    <div class="pay_column">
                        <p></p>
                        <select name="payment_method" id="payment_method">
                            <option value="" disabled selected>Please select an option</option>
                            <option value="card">Credit card</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" name="propertyID" value="<?php echo htmlspecialchars($_SESSION['propertyID'] ?? ''); ?>" />
            <input class="button" type="submit" name="make_payment" value="Make Payment">
        </form>
    </main>
</body>

</html>
<?php ?>