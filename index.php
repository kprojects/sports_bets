<?php
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the date from the query string, defaulting to today in America/New_York timezone
$bet_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Retrieve all bets for the specified date with their associated legs, grouped by bet_id
$stmt = $pdo->prepare("
    SELECT * FROM bets
    WHERE DATE(date) = ?
    ORDER BY bet_id ASC, id ASC
");
$stmt->execute([$bet_date]);
$bets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group bets by bet_id
$grouped_bets = [];
foreach ($bets as $bet) {
    $grouped_bets[$bet['bet_id']][] = $bet;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leg_id']) && isset($_POST['status'])) {
    $leg_id = $_POST['leg_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE bets SET status = ? WHERE id = ?");
    $stmt->execute([$status, $leg_id]);

    header("Location: bets.php?date=$bet_date");
    exit;
}

// Handle deleting a bet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bet_id'])) {
    $delete_bet_id = $_POST['delete_bet_id'];

    $stmt = $pdo->prepare("DELETE FROM bets WHERE bet_id = ?");
    $stmt->execute([$delete_bet_id]);

    header("Location: bets.php?date=$bet_date");
    exit;
}

// Handle creating a new bet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parlay_name']) && isset($_POST['game_info'])) {
    $parlay_name = $_POST['parlay_name'];
    $game_info = $_POST['game_info'];
    $odds = $_POST['odds'];
    $bet_amount = $_POST['bet_amount'];
    $potential_payout = $_POST['potential_payout'];

    // Insert each leg into the bets table under the same bet_id
    $stmt = $pdo->prepare("SELECT MAX(bet_id) AS max_bet_id FROM bets");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $new_bet_id = $row['max_bet_id'] + 1;

    foreach ($_POST['legs'] as $leg) {
        if (!empty($leg['player_name']) && !empty($leg['team']) && !empty($leg['condition'])) {
            $stmt = $pdo->prepare("INSERT INTO bets (bet_id, date, game_info, parlay_name, player_name, team, `condition`, status, odds, bet_amount, potential_payout) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?, ?, ?)");
            $stmt->execute([$new_bet_id, $bet_date, $game_info, $parlay_name, $leg['player_name'], $leg['team'], $leg['condition'], $odds, $bet_amount, $potential_payout]);
        }
    }

    header("Location: bets.php?date=$bet_date");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bets for <?= htmlspecialchars($bet_date) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .bets-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .bet {
            background-color: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .bet h2 {
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bet .delete-button {
            background: none;
            border: none;
            color: #f00;
            cursor: pointer;
            font-size: 1.2em;
        }
        .status-button {
            background-color: transparent;
            color: #333; /* Slightly darker than the background */
            border: none;
            padding: 0 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .status-button:hover {
            color: #666; /* Slightly lighter on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bets for <?= htmlspecialchars($bet_date) ?></h1>
        
        <!-- Link back to index -->
        <p><a href="index.php" style="color: #268bd2; text-decoration: none; font-weight: bold;">&larr; Back to Index</a></p>

        <?php if (empty($grouped_bets)): ?>
            <p>No bets found for this date.</p>
        <?php else: ?>
            <div class="bets-grid">
                <?php foreach ($grouped_bets as $bet_id => $legs): ?>
                    <div class="bet">
                        <h2>
                            <?= htmlspecialchars($legs[0]['parlay_name']) ?> <?= $legs[0]['odds'] > 0 ? '+' . htmlspecialchars($legs[0]['odds']) : htmlspecialchars($legs[0]['odds']) ?>
                            <form method="POST" action="bets.php?date=<?= htmlspecialchars($bet_date) ?>" style="display:inline;">
                                <input type="hidden" name="delete_bet_id" value="<?= htmlspecialchars($bet_id) ?>">
                                <button type="submit" class="delete-button">🗑️</button>
                            </form>
                        </h2>
                        <p class="game-info"><?= htmlspecialchars($legs[0]['game_info']) ?></p>
                        <p class="amounts"><strong>Bet Amount:</strong> $<?= number_format($legs[0]['bet_amount'], 2) ?> | <strong>Potential Payout:</strong> $<?= number_format($legs[0]['potential_payout'], 2) ?></p>
                        <ul>
                            <?php foreach ($legs as $leg): ?>
                                <li class="<?= strtolower(htmlspecialchars($leg['status'])) ?>">
                                    <strong><?= htmlspecialchars($leg['player_name']) ?> (<?= htmlspecialchars($leg['team']) ?>):</strong> <?= htmlspecialchars($leg['condition']) ?>
                                    <?php if ($leg['status'] === 'Pending'): ?>
                                        <form method="POST" action="bets.php?date=<?= htmlspecialchars($bet_date) ?>" style="display:inline;">
                                            <input type="hidden" name="leg_id" value="<?= htmlspecialchars($leg['id']) ?>">
                                            <input type="hidden" name="status" value="Completed">
                                            <button type="submit" class="status-button">🔨</button>
                                        </form>
                                    <?php elseif ($leg['status'] === 'Completed'): ?>
                                        <form method="POST" action="bets.php?date=<?= htmlspecialchars($bet_date) ?>" style="display:inline;">
                                            <input type="hidden" name="leg_id" value="<?= htmlspecialchars($leg['id']) ?>">
                                            <input type="hidden" name="status" value="Pending">
                                            <button type="submit" class="status-button">×</button>
                                        </form>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2>Create a New Bet</h2>
        <form method="POST">
            <input type="hidden" name="date" value="<?= htmlspecialchars($bet_date) ?>">
            <label for="parlay_name">Parlay Name:</label>
            <input type="text" name="parlay_name" id="parlay_name" required>
            
            <label for="game_info">Game Info:</label>
            <input type="text" name="game_info" id="game_info" required>
            
            <label for="odds">Odds:</label>
            <input type="number" name="odds" id="odds" required>
            
            <label for="bet_amount">Bet Amount:</label>
            <input type="number" name="bet_amount" id="bet_amount" step="0.01" required>
            
            <label for="potential_payout">Potential Payout:</label>
            <input type="number" name="potential_payout" id="potential_payout" step="0.01" required>

            <h3>Legs</h3>
            <div id="legs-container"></div>
            <button type="button" onclick="addLeg()">Add Leg</button>
            
            <button type="submit">Create Bet</button>
        </form>
    </div>
    <script>
        function addLeg() {
            const legContainer = document.getElementById('legs-container');
            const legIndex = legContainer.children.length;

            const legDiv = document.createElement('div');
            legDiv.className = 'leg-input';

            legDiv.innerHTML = `
                <input type="text" name="legs[${legIndex}][player_name]" placeholder="Player Name" required>
                <input type="text" name="legs[${legIndex}][team]" placeholder="Team" required>
                <input type="text" name="legs[${legIndex}][condition]" placeholder="Condition" required>
                <button type="button" onclick="removeLeg(this)">Remove</button>
            `;

            legContainer.appendChild(legDiv);
        }

        function removeLeg(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>
