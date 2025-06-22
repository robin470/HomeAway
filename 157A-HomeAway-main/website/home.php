<?php
session_start();
include 'database.php';

if (isset($_SESSION['id']) && isset($_SESSION['userID'])) {
    $cityName = '';
    $results = null;

    try {
        if (isset($_GET['city'])) {
            $cityName = trim(htmlspecialchars($_GET['city']));

            // Query to fetch properties based on city
            $stmt = $conn->prepare("SELECT
                    p.propertyID, p.price, p.type, p.num_bedrooms, p.num_bathrooms,
                    p.max_guests, p.street, a.city
                FROM
                    Property p
                INNER JOIN
                    Address a ON p.street = a.street
                WHERE
                    LOWER(a.city) LIKE LOWER(?)
                ");

            $searchParam = "%" . $cityName . "%";
            $stmt->bind_param("s", $searchParam);
            $stmt->execute();
            $results = $stmt->get_result();
        } else {
            // Load default properties when no search is performed
            $stmt = $conn->prepare("SELECT
                    p.propertyID, p.price, p.type, p.num_bedrooms, p.num_bathrooms,
                    p.max_guests, p.street, a.city
                FROM
                    Property p
                INNER JOIN
                    Address a ON p.street = a.street
                ");

            $stmt->execute();
            $results = $stmt->get_result();
        }

        if (!$results || $results->num_rows === 0) {
            error_log("No properties found.");
        }
    } catch (Exception $e) {
        error_log("Database query failed: " . $e->getMessage());
        die("An error occurred while fetching data. Please try again later.");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeAway</title>
    <link rel="stylesheet" href="css/homeStyle.css">
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
                    placeholder="Location: San Jose CA"
                    value="<?= htmlspecialchars($cityName) ?>">
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
        <?php if ($results && $results->num_rows > 0): ?>
            <?php while ($row = $results->fetch_assoc()): ?>
                <div class="home-listing">
                <h2>
                    <a href="property.php?propertyID=<?= htmlspecialchars($row['propertyID']) ?>">
                        Property ID: <?= htmlspecialchars($row['propertyID']) ?> - <?= htmlspecialchars($row['type']) ?> | View Property
                    </a>
                </h2>

                    <div class="home-details">
                        <div class="info_column">
                            <p><strong>Price per day:</strong> $<?= htmlspecialchars($row['price']) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars($row['street']) ?>, <?= htmlspecialchars($row['city']) ?></p>
                        </div>
                        <div class="info_column">
                            <p><strong>Details:</strong> <?= htmlspecialchars($row['num_bedrooms']) ?> bed, <?= htmlspecialchars($row['num_bathrooms']) ?> bath</p>
                            <p><strong>Max Guests:</strong> <?= htmlspecialchars($row['max_guests']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No properties found.</p>
        <?php endif; ?>
    </main>

</body>
</html>

<?php
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>