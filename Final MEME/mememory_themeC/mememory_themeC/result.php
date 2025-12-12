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
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Result</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-preload">
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
<audio id="resultSound" src="sounds/result.mp3" preload="auto"></audio>
<script src="transition.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const resultSound = document.getElementById('resultSound');
  if (!resultSound) return;
  resultSound.volume = 0.45;
  const tryPlay = () => {
    const playPromise = resultSound.play();
    if (playPromise && typeof playPromise.then === 'function') {
      playPromise.catch(()=>{});
    }
  };
  tryPlay();
  document.body.addEventListener('pointerdown', () => {
    if (resultSound.paused) tryPlay();
  }, { once: true });
});
</script>
</body>
</html>
