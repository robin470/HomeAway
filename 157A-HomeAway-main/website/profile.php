<?php
session_start();
//include 'database.php';
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=HomeAwayDB;charset=utf8", 'root', 'rootp');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Dynamic userID (use session or GET parameter)
$userID = $_SESSION['userID'] ?? 'defaultUser'; // Replace 'defaultUser' with an appropriate fallback or get from session
// Fetch user information
$stmt = $pdo->prepare("SELECT userID, first_name, last_name, email, phone_number FROM User WHERE userID = :userID");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Property
$stmt = $pdo->prepare("SELECT * FROM Property WHERE userID = :userID");
$stmt->execute([':userID' => $userID]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch upcoming reservations for the user
$stmt = $pdo->prepare("
    SELECT b.reservation_start, b.reservation_end
    FROM Booking b
    WHERE b.userID = :userID
    ORDER BY b.reservation_start ASC
");
$stmt->execute([':userID' => $userID]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$user) {
    die("User not found.");
}
// delete property button
if (isset($_POST['delete'])) {
    if (isset($_POST['propertyID'])) {
        $propertyID = intval($_POST['propertyID']);
        $street = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
        try {
            // Delete the property from the database
            $stmt1 = $pdo->prepare("DELETE FROM Address WHERE Street = :street");
            $stmt1->execute([
                ':street' => $street
            ]);
            $stmt = $pdo->prepare("DELETE FROM Property WHERE propertyID = :propertyID AND userID = :userID");
            $stmt->execute([
                ':propertyID' => $propertyID,
                ':userID' => $userID
            ]);
            // Redirect to refresh the list of properties
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } catch (PDOException $e) {
            echo "<p class='error'>Error deleting property: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>No property selected for deletion.</p>";
    }
}
// submit post property
// TODO: connect to database and disable button if fields aren't completed
if (isset($_POST['submit'])) {
    // Ensure all fields are completed
    $requiredFields = [
        'property_name',
        'property_type',
        'no_bedrooms',
        'no_bathrooms',
        'max_occupancy',
        'price_day',
        'street',
        'city',
        'state',
        'postal_code',
        'country'
    ];
    // Check if all required fields are set and not empty
    $allFieldsCompleted = true;
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $allFieldsCompleted = false;
            break;
        }
    }
    if ($allFieldsCompleted) {
        try {
            // Sanitize and store input values
            $propertyName = htmlspecialchars(trim($_POST['property_name']));
            $propertyType = htmlspecialchars(trim($_POST['property_type']));
            $noBedrooms = intval($_POST['no_bedrooms']);
            $noBathrooms = intval($_POST['no_bathrooms']);
            $maxOccupancy = intval($_POST['max_occupancy']);
            $priceDay = floatval($_POST['price_day']);
            $street = htmlspecialchars(trim($_POST['street']));
            $city = htmlspecialchars(trim($_POST['city']));
            $state = htmlspecialchars(trim($_POST['state']));
            $postalCode = htmlspecialchars(trim($_POST['postal_code']));
            $country = htmlspecialchars(trim($_POST['country']));
            $picture = htmlspecialchars(trim($_POST['picture']));
            // Connect to the database
            $pdo = new PDO("mysql:host=localhost;dbname=HomeAwayDB;charset=utf8", 'root', 'rootp');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // User ID from session
            $userID = $user['userID'] ?? null;
            if (!$userID) {
                throw new Exception("User ID is missing. Please log in.");
            }
            // ****************
            //Test property ID, should remove later
            // Insert the new property
            $stmt1 = $pdo->prepare("
                INSERT INTO Address(
                    street, city, postal_code, state, country
                ) VALUES (
                    :street, :city, :zipCode, :state, :country
                )
            ");
            $stmt1->execute([
                ':street' => $street,
                ':city' => $city,
                ':state' => $state,
                ':country' => $country,
                ':zipCode' => $postalCode,
            ]);
            $stmt = $pdo->prepare("
                INSERT INTO Property (
                    price, type, num_bedrooms, num_bathrooms, max_guests, street,userID
                ) VALUES (
                    :price, :type, :num_bedrooms, :num_bathrooms, :max_guests, :street, :userID
                )
            ");
            $stmt->execute([
                ':price' => $priceDay,
                ':type' => $propertyType,
                ':num_bedrooms' => $noBedrooms,
                ':num_bathrooms' => $noBathrooms,
                ':max_guests' => $maxOccupancy,
                ':street' => $street,
                ':userID' => $userID
            ]);
            $propertyID = $pdo->lastInsertId();
            // Insert the photo into the Photo table
            $stmtPhoto = $pdo->prepare("
                INSERT INTO Photo (
                    propertyID, image_data
                ) VALUES (
                    :propertyID, :image_data
                )
            ");
            $stmtPhoto->execute([
                ':propertyID' => $propertyID,
                ':image_data' => $picture,
            ]);
            // Redirect to avoid resubmission dialog
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } catch (PDOException $e) {
            echo "<p class='error'>Error adding property: " . htmlspecialchars($e->getMessage()) . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p class='error'>All fields must be completed.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeAway</title>
    <link rel="stylesheet" href="css/profileStyle.css" />
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
        <!-- User Info -->
        <div class="section">
            <div class="title">Profile</div>
            <div class="row">
                <!-- User Personal Info -->
                <div class="container">
                    <div class="subheading">Contact Information</div>
                    <div class="detail">
                        <div class="text">Name:</div>
                        <div class="text"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="text">Email:</div>
                        <div class="text"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="text">Phone:</div>
                        <div class="text"><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>
            <!-- Add Property Form -->
            <div class="container">
                <div class="subheading">Add Owned Property</div>
                <div class="description">Looking to rent out your home? Enter the details here.</div>
                <form method="POST" action="">
                    <div class="row">
                        <!-- Left side of form -->
                        <div class="detail">
                            <div class="label_column">
                                <label>Property Name:</label>
                                <label>Property Type:</label>
                                <label>No. Bedrooms:</label>
                                <label>No. Bathrooms:</label>
                                <label>Max. Occupancy:</label>
                                <label>Price ($) / day:</label>
                            </div>
                            <div class="input_column">
                                <input class="text" type="text" id="property_name" name="property_name" required>
                                <input class="text" type="text" id="property_type" name="property_type" required>
                                <input class="text" type="number" id="no_bedrooms" name="no_bedrooms" required>
                                <input class="text" type="number" id="no_bathrooms" name="no_bathrooms" required>
                                <input class="text" type="number" id="max_occupancy" name="max_occupancy" required>
                                <input class="text" type="number" id="price_day" name="price_day" required>
                            </div>
                        </div>
                        <!-- Right side of form -->
                        <div class="detail">
                            <div class="label_column">
                                <label>Street:</label>
                                <label>City:</label>
                                <label>State:</label>
                                <label>Postal Code:</label>
                                <label>Country:</label>
                                <label>Picture URL:</label>
                            </div>
                            <div class="input_column">
                                <input class="text" type="text" id="street" name="street" required>
                                <input class="text" type="text" id="city" name="city" required>
                                <input class="text" type="text" id="state" name="state" required>
                                <input class="text" type="text" id="postal_code" name="postal_code" required>
                                <input class="text" type="text" id="country" name="country" required>
                                <input class="text" type="text" id="picture" name="picture" required>
                            </div>
                        </div>
                    </div>
                    <!-- Post Property Button -->
                    <input class="button" type="submit" name="submit" value="Post Property">
                </form>
            </div>
        </div>
        </div>
        <!-- Owned Property Info -->
        <div class="section">
            <div class="title">Owned Properties</div>
            <div class="property">
                <!-- A property item (consists of property and delete button) -->
                <?php if ($properties): ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="property_item">
                            <!-- An Owned Property -->
                            <div class="home-listing">
                                <h2><?php echo htmlspecialchars($property['type']); ?></h2>
                                <div class="home-details">
                                    <div class="details">
                                        <p><strong>Details:</strong> <?php echo htmlspecialchars($property['num_bedrooms']); ?> bed, <?php echo htmlspecialchars($property['num_bathrooms']); ?> bath</p>
                                        <p><?php echo htmlspecialchars($property['street']); ?></p>
                                        <div class="stars">★★★★★</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Delete Property Button -->
                            <form method="post">
                                <input type="hidden" name="street" value="<?php echo htmlspecialchars($property['street']); ?>">
                                <input type="hidden" name="propertyID" value="<?php echo htmlspecialchars($property['propertyID']); ?>">
                                <input type="submit" name="delete" class="delete_button" value="DELETE">
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No properties owned. Add a property to see it here.</p>
                <?php endif; ?>
            </div>
            <!-- Upcoming Reservations -->
            <div class="upcoming_reservations">
                <div class="label"><a href="history.php">Upcoming Reservations </a></div>
                <div class="row">
                    <?php if ($reservations): ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="reservation_container">
                                <div class="detail">
                                    <div class="text">Start Date:</div>
                                    <div class="text"><?php echo htmlspecialchars($reservation['reservation_start']); ?></div>
                                </div>
                                <div class="detail">
                                    <div class="text">End Date:</div>
                                    <div class="text"><?php echo htmlspecialchars($reservation['reservation_end']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming reservations found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </main>
</body>
</html>