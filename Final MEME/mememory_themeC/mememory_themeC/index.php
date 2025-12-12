<?php
session_start();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>MEMEmory - Start (Forest Cottage Theme)</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-preload">
  <div class="bg-forest"></div>
  <div class="container center-container">
    <h1 class="title enhanced-title">MEMEmory</h1>
    <form method="post" action="game.php" class="panel enhanced-panel">
      <label class="enhanced-label">Player 1 name
        <input type="text" name="p1" required placeholder="Player 1" class="enhanced-input">
      </label>
      <label class="enhanced-label">Player 2 name
        <input type="text" name="p2" required placeholder="Player 2" class="enhanced-input">
      </label>

      <label class="enhanced-label">Difficulty
        <select name="difficulty" class="enhanced-select">
          <option value="easy">Easy (6 cards)</option>
          <option value="medium">Medium (16 cards)</option>
          <option value="hard">Hard (24 cards)</option>
        </select>
      </label>

      <label class="enhanced-label">Category
        <select name="category" required class="enhanced-select">
          <option value="celebrity">Celebrity</option>
          <option value="politician">Politician</option>
          <option value="tiktok">TikTok memes</option>
          <option value="malupiton">Malupiton Verse</option>
          <option value="local">Local memes</option>
        </select>
      </label>

      <p class="note enhanced-note">Turn timer: <strong>15 seconds</strong></p>

      <button type="submit" class="btn-cta enhanced-btn-cta">Start Game</button>
    </form>

   </div>
<script src="transition.js"></script>
</body>
</html>
