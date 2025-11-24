<?php
session_start();
require_once __DIR__ . '/classes/Game.php';
if (!isset($_SESSION['memegame'])) { header('Location: index.php'); exit; }
$game = Game::fromSerializable($_SESSION['memegame']);
$winner = $game->getWinner();
$wname = $winner ? $winner->getName() : 'Draw';
$score0 = $game->getPlayers()[0]->getScore();
$score1 = $game->getPlayers()[1]->getScore();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Result</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="bg-forest"></div>
<div class="container result enhanced-result-container">
  <h1 class="enhanced-result-title">Game Over</h1>
  <h2 class="enhanced-winner-name"><?= $winner ? "Winner: ".htmlspecialchars($wname) : "It's a Draw!" ?></h2>
  <p class="enhanced-score <?= $winner && $wname === htmlspecialchars($game->getPlayers()[0]->getName()) ? 'winner' : 'loser' ?>">
    <?= htmlspecialchars($game->getPlayers()[0]->getName()) ?>: <?= $score0 ?></p>
  <p class="enhanced-score <?= $winner && $wname === htmlspecialchars($game->getPlayers()[1]->getName()) ? 'winner' : 'loser' ?>">
    <?= htmlspecialchars($game->getPlayers()[1]->getName()) ?>: <?= $score1 ?></p>
  <a href="index.php"><button class="btn-cta enhanced-btn-cta">Play Again</button></a>
</div>
</body>
</html>
