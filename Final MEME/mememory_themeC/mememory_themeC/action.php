<?php
session_start();
require_once __DIR__ . '/classes/Game.php';
header('Content-Type: application/json');
if (!isset($_SESSION['memegame'])) { echo json_encode(['error'=>'no_game']); exit; }
$data = $_SESSION['memegame'];
$game = Game::fromSerializable($data);
$action = $_POST['action'] ?? '';
if ($action === 'match') {
    $a = intval($_POST['a']); $b = intval($_POST['b']);
    $cardA = $game->getDeck()->getCardByIndex($a);
    $cardB = $game->getDeck()->getCardByIndex($b);
    if (!$cardA || !$cardB) { echo json_encode(['error'=>'invalid_card']); exit; }
    if ($cardA->getId() === $cardB->getId()) {
        $game->markMatched($a, $b);
        $_SESSION['memegame'] = $game->toSerializable();
        $finished = $game->isFinished();
        echo json_encode(['result'=>'match','finished'=>$finished,'scores'=>[$game->getPlayers()[0]->getScore(),$game->getPlayers()[1]->getScore()]]);
        exit;
    } else {
        $game->switchTurn();
        $_SESSION['memegame'] = $game->toSerializable();
        echo json_encode(['result'=>'nomatch','current'=>$game->getCurrentPlayerIndex(),'scores'=>[$game->getPlayers()[0]->getScore(),$game->getPlayers()[1]->getScore()]]);
        exit;
    }
}
if ($action === 'switch') {
    $game->switchTurn();
    $_SESSION['memegame'] = $game->toSerializable();
    echo json_encode(['current'=>$game->getCurrentPlayerIndex()]);
    exit;
}
if ($action === 'finish') {
    $winner = $game->getWinner();
    $w = $winner ? $winner->getName() : null;
    echo json_encode(['winner'=>$w,'scores'=>[$game->getPlayers()[0]->getScore(),$game->getPlayers()[1]->getScore()]]);
    exit;
}
echo json_encode(['error'=>'unknown_action']);
