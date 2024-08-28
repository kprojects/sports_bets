<?php
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve distinct dates from bets table
$stmt = $pdo->prepare("SELECT DISTINCT DATE(date) AS bet_date FROM bets ORDER BY bet_date DESC");
$stmt->execute();
$dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's date
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Bets</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dates-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5-column layout */
            gap: 20px;
            margin-bottom: 20px;
        }

        .date-card {
            background-color: #2c2c2c; /* Similar to bets.php card style */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .date-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .date-card a {
            text-decoration: none;
            color: #268bd2; /* Blue color for links */
            font-weight: bold;
        }

        .date-card a:hover {
            color: #2aa198; /* Lighter color on hover */
        }

        .create-button {
            display: inline-block;
            background-color: #268bd2;
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .create-button:hover {
            background-color: #2aa198; /* Cyan on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recent Bets</h1>

        <!-- Create Today's Bet Page -->
        <?php if (!in_array(['bet_date' => $today], $dates)): ?>
            <a href="bets.php?date=<?= htmlspecialchars($today) ?>" class="create-button">Create Today's Bet Page</a>
        <?php endif; ?>

        <!-- Display Past Dates in a Grid -->
        <div class="dates-grid">
            <?php foreach ($dates as $date): ?>
                <div class="date-card">
                    <a href="bets.php?date=<?= htmlspecialchars($date['bet_date']) ?>">
                        <?= htmlspecialchars($date['bet_date']) ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
