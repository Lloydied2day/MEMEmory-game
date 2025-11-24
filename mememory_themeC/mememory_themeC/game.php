<?php
session_start();
require_once __DIR__ . '/classes/Game.php';
require_once __DIR__ . '/classes/Easy.php';
require_once __DIR__ . '/classes/Medium.php';
require_once __DIR__ . '/classes/Hard.php';

$memesPath = __DIR__ . '/memes';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p1 = trim($_POST['p1']); $p2 = trim($_POST['p2']);
    $diff = $_POST['difficulty']; $category = $_POST['category'];
    $difficulty = $diff === 'easy' ? new Easy() : ($diff === 'medium' ? new Medium() : new Hard());
    $game = new Game($difficulty, $category, [$p1, $p2], $memesPath);
    $_SESSION['memegame'] = $game->toSerializable();
    header('Location: game.php'); exit;
}
if (!isset($_SESSION['memegame'])) { header('Location: index.php'); exit; }
$game = Game::fromSerializable($_SESSION['memegame']);
$deck = $game->getDeck()->getCards();
$players = $game->getPlayers();
$current = $game->getCurrentPlayerIndex();
$timePerTurn = $_SESSION['memegame']['timePerTurn'] ?? 15;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>MEMEmory - Game</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="bg-forest"></div>
<div class="container enhanced-container">
  <h2 class="small-title enhanced-small-title">MEMEmory — <?= htmlspecialchars($_SESSION['memegame']['difficulty']) ?> / <?= htmlspecialchars($_SESSION['memegame']['category']) ?></h2>
  <div class="players enhanced-players">
    <div id="p0" class="player <?= $current===0 ? 'active enhanced-active' : ''?>">
      <strong><?= htmlspecialchars($players[0]->getName()) ?></strong>
      <div>Score: <span id="score0"><?= $players[0]->getScore() ?></span></div>
      <div>Time: <span id="time0"><?= $players[0]->getTimeLeft() ?></span>s</div>
    </div>
    <div id="p1" class="player <?= $current===1 ? 'active enhanced-active' : ''?>">
      <strong><?= htmlspecialchars($players[1]->getName()) ?></strong>
      <div>Score: <span id="score1"><?= $players[1]->getScore() ?></span></div>
      <div>Time: <span id="time1"><?= $players[1]->getTimeLeft() ?></span>s</div>
    </div>
  </div>

  <div id="board" class="board enhanced-board" data-time="<?= $timePerTurn ?>">
    <?php foreach ($deck as $card): ?>
      <div class="card enhanced-card" data-index="<?= $card->getIndex() ?>" data-id="<?= $card->getId() ?>">
        <div class="inside">
          <div class="front"><img src="<?= htmlspecialchars($card->getImage()) ?>" alt="meme"></div>
          <div class="back">?</div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="controls enhanced-controls">
    <button id="resetBtn" class="btn-cta enhanced-btn-cta">Play Again</button>
  </div>

  <audio id="flipSound" src="sounds/flip.mp3"></audio>
  <audio id="matchSound" src="sounds/match.mp3"></audio>
  <audio id="wrongSound" src="sounds/wrong.mp3"></audio>
  <audio id="winSound" src="sounds/win.mp3"></audio>
</div>

<script src="script.js"></script>
</body>
</html>
