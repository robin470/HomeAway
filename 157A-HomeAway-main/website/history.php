<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=HomeAwaydb;charset=utf8", 'root', 'rootp');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userID = $_SESSION['userID'] ?? 'defaultUser'; // Replace 'defaultUser' with an appropriate fallback or get from session


$stmtHistory = $pdo->prepare("SELECT userID FROM History WHERE userID = :userID");
$stmtHistory->execute(['userID' => $userID]);


$history = $stmtHistory->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT 
        b.reservation_start, 
        b.reservation_end, 
        p.type AS property_type, 
        p.num_bedrooms, 
        p.num_bathrooms, 
        p.street
    FROM Booking b
    JOIN Property p ON b.propertyID = p.propertyID
    WHERE b.userID = :userID
    ORDER BY b.reservation_start DESC
");
$stmt->execute([':userID' => $userID]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeAway</title>
    <link rel="stylesheet" href="css/historyStyle.css" />
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
        <div class="title">Reservation History</div>
        <div class="history">
            <?php if ($history): ?>
                <?php foreach ($history as $reservation): ?>
                    <!-- A Reserved Property -->
                    <div class="section">
                        <!-- Reservation time period -->
                        <div class="text">
                            <?php echo htmlspecialchars(
                                date("m/d/Y", strtotime($reservation['reservation_start'])) . " - " .
                                    date("m/d/Y", strtotime($reservation['reservation_end']))
                            ); ?>
                        </div>
                        <!-- Property details container -->
                        <div class="home-listing">
                            <h2><?php echo htmlspecialchars($reservation['property_type']); ?></h2>
                            <div class="home-details">
                                <div class="details">
                                    <p><strong>Details:</strong>
                                        <?php echo htmlspecialchars($reservation['num_bedrooms']); ?> bed,
                                        <?php echo htmlspecialchars($reservation['num_bathrooms']); ?> bath
                                    </p>
                                    <p><?php echo htmlspecialchars($reservation['street']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reservation history found.</p>
            <?php endif; ?>
        </div>
        </div>
    </main>
</body>

</html>
<? php ?>