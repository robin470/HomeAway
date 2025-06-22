<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "rootp";
$dbname = "HOMEAWAYDB";
$conn = "";

/*
// reserve property button
if (isset($_POST['reserve'])) {
    // TODO: need to save the data to payment page
    header("Location: cart.php");
    exit();
}
*/
// Connect to the database
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the propertyID from the URL
if (isset($_GET['propertyID'])) {
    $propertyID = $_GET['propertyID'];
} else {
    echo "Property not found.";
    exit();
}


// Query to fetch property details
// Fetch property details
$stmtProperty = $pdo->prepare("SELECT * FROM Property WHERE propertyID = :propertyID");
$stmtProperty->execute(['propertyID' => $propertyID]);
$property = $stmtProperty->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "Property not found.";
    exit();
}

// Query to fetch photos associated with the property
$stmtPhotos = $pdo->prepare("SELECT image_data FROM Photo WHERE propertyID = :propertyID");
$stmtPhotos->execute(['propertyID' => $propertyID]);
$photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

// Display the list of review
//$propertyID = $property['propertyID'] ?? null;  // just for test, need to modify later
$stmt = $pdo->prepare("SELECT r.content, r.date, r.rating, r.userID
                       FROM Review r
                       WHERE r.propertyID = :propertyID
                       ORDER BY r.date DESC");
$stmt->execute(['propertyID' => $propertyID]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// post review button
if (isset($_POST['review'])) {
    // Get the rating if provided, otherwise set to NULL
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $content = isset($_POST['textarea']) ? htmlspecialchars(trim($_POST['textarea'])) : '';

    // Ensure the content is not empty
    if (!empty($content)) {
        // Ensure propertyID and userID are defined
        $propertyID = $property['propertyID'] ?? null;
        $userID = $_SESSION['userID'] ?? null;

        if ($propertyID && $userID) {
            try {
                // Insert the review into the database
                $stmt = $pdo->prepare("INSERT INTO Review (content, date, rating, propertyID, userID)
                                       VALUES (:content, CURDATE(), :rating, :propertyID, :userID)");
                $stmt->execute([
                    ':content' => $content,
                    ':rating' => $rating, // NULL if no rating is provided
                    ':propertyID' => $propertyID,
                    ':userID' => $userID,
                ]);

                // Redirect to avoid resubmission
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } catch (PDOException $e) {
                echo "<p class='error'>Error saving review: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p class='error'>Property ID or User ID is missing.</p>";
        }
    } else {
        echo "<p class='error'>Please provide content for your review.</p>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeAway</title>
    <link rel="stylesheet" href="css/propertyStyle.css" />
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
        <!-- Property -->
        <div class="official_info">
            <!-- Property Quick Details -->
            <div class="summary">
                <div class="property_name"><?php echo htmlspecialchars($property['type']); ?></div>
                <div class="stars">★★★★★</div>
                <div class="text">$<?php echo number_format($property['price'], 2); ?> / day</div>
                <div class="text"><?php echo htmlspecialchars($property['street']); ?></div>
            </div>
            <!-- Photos -->
            <div class="photos">
                <?php foreach ($photos as $photo): ?>
                    <img src="<?php echo htmlspecialchars($photo['image_data']); ?>" alt="Property Photo">
                <?php endforeach; ?>
            </div>
            <!-- Property Info -->
            <div class="property_info">

                <div class="info_column">
                    <div class="detail">
                        <div class="label">Property Type:</div>
                        <div class="text"><?php echo htmlspecialchars($property['type']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="label">Property ID:</div>
                        <div class="text" id = "propertyID"><?php echo htmlspecialchars($property['propertyID']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="label">Host ID:</div>
                        <div class="text"><?php echo htmlspecialchars($property['userID']); ?></div>
                    </div>
                </div>
                <div class="info_column">
                    <div class="detail">
                        <div class="label">Number of Bedrooms:</div>
                        <div class="text"><?php echo htmlspecialchars($property['num_bedrooms']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="label">Number of Bathrooms:</div>
                        <div class="text"><?php echo htmlspecialchars($property['num_bathrooms']); ?></div>
                    </div>
                    <div class="detail">
                        <div class="label">Max. Occupancy:</div>
                        <div class="text"><?php echo htmlspecialchars($property['max_guests']); ?></div>
                    </div>
                </div>
                <!-- Reserve Button -->
                <form method="post" action="cart.php">
                <input type="hidden" name="propertyID" value="<?php echo htmlspecialchars($property['propertyID']); ?>" />
                    <input type="submit" name="reserve" class="button" value="Reserve Property" />
                </form>
            </div>

            <!-- Reviews Section -->
            <div class="reviews_section">
                <div class="subheading">Write Review</div>
                <form class="write_reviews_section" action="" method="post">
                    <div class="detail">
                        <label for="rating">Select your rating:</label>
                        <select name="rating" id="rating">
                            <option value="" disabled selected>Please select an option</option>
                            <option value="1">1 star</option>
                            <option value="2">2 stars</option>
                            <option value="3">3 stars</option>
                            <option value="4">4 stars</option>
                            <option value="5">5 stars</option>
                        </select>
                    </div>
                    <textarea name="textarea" id="textarea" rows="4" cols="50" placeholder="Describe your experience..."></textarea>
                    <input class="button" type="submit" name="review" value="Post Review">
                </form>
            </div>

            <!-- List of Reviews -->
            <div class="reviews_section">
                <div class="subheading">Reviews</div>
                <?php if ($reviews): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="container">
                            <div class="review_user"><?php echo htmlspecialchars($review['userID']); ?></div>
                            <div class="rating_date">
                                <div class="stars"><?php echo str_repeat("★", $review['rating']); ?></div>
                                <div class="date"><?php echo htmlspecialchars($review['date']); ?></div>
                            </div>
                            <div class="text"><?php echo htmlspecialchars($review['content']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews yet. Be the first to review!</p>
                <?php endif; ?>
            </div>
    </main>
</body>

</html>
<? php ?>