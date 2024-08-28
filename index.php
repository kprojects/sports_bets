<?php
include 'config.php';

date_default_timezone_set('America/New_York'); // Set the timezone to America/New_York

// Get the list of unique dates for which bets exist
$stmt = $pdo->prepare("
    SELECT DISTINCT DATE(date) AS bet_date
    FROM bets
    ORDER BY bet_date DESC
    LIMIT 7
");
$stmt->execute();
$bet_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine today's date in the America/New_York timezone
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Bets Index</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Recent Bets</h1>
        <ul class="bet-dates-list">
            <?php if (empty($bet_dates)): ?>
                <li>No bet dates found.</li>
            <?php else: ?>
                <?php foreach ($bet_dates as $date): ?>
                    <li><a href="bets.php?date=<?= htmlspecialchars($date['bet_date']) ?>"><?= htmlspecialchars($date['bet_date']) ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <h2>Create a New Bet Page for Today</h2>
        <a href="bets.php?date=<?= htmlspecialchars($today) ?>" class="create-bet-page-button">Create Today's Bet Page</a>
    </div>
</body>
</html>
