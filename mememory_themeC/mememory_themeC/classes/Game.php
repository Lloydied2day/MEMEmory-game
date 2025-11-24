<?php
require_once __DIR__ . '/Deck.php';
require_once __DIR__ . '/Player.php';
require_once __DIR__ . '/Easy.php';
require_once __DIR__ . '/Medium.php';
require_once __DIR__ . '/Hard.php';
class Game {
    private $deck; private $players=[]; private $currentPlayer=0; private $difficulty; private $category; private $timePerTurn=15;
    public function __construct(Difficulty $difficulty, string $category, array $playerNames, string $baseMemesPath) {
        $this->difficulty=$difficulty; $this->category=$category; $this->timePerTurn=$difficulty->getTimePerTurn();
        $pairs = $difficulty->getPairs();
        $folder = rtrim($baseMemesPath, '/\\') . '/' . $category;
        $deck = new Deck();
        $deck->generateFromFolder($folder, $pairs);
        $this->deck = $deck;
        foreach ($playerNames as $name) $this->players[] = new Player($name, $this->timePerTurn);
    }
    public function getDeck(): Deck { return $this->deck; }
    public function getPlayers(): array { return $this->players; }
    public function getCurrentPlayerIndex(): int { return $this->currentPlayer; }
    public function switchTurn() {
        $this->currentPlayer = ($this->currentPlayer + 1) % count($this->players);
        $this->players[$this->currentPlayer]->setTimeLeft($this->timePerTurn);
    }
    public function markMatched(int $indexA, int $indexB) {
        $this->deck->setMatchedByIndex($indexA);
        $this->deck->setMatchedByIndex($indexB);
        $this->players[$this->currentPlayer]->incrementScore(1);
    }
    public function isFinished(): bool { return $this->deck->allMatched(); }
    public function getWinner(): ?Player {
        $p0 = $this->players[0]->getScore(); $p1 = $this->players[1]->getScore();
        if ($p0 === $p1) return null;
        return $p0 > $p1 ? $this->players[0] : $this->players[1];
    }
    public function toSerializable(): array {
        return [
            'deck'=>$this->deck->toSerializable(),
            'players'=>[
                ['name'=>$this->players[0]->getName(),'score'=>$this->players[0]->getScore(),'time'=>$this->players[0]->getTimeLeft()],
                ['name'=>$this->players[1]->getName(),'score'=>$this->players[1]->getScore(),'time'=>$this->players[1]->getTimeLeft()],
            ],
            'current'=>$this->currentPlayer,
            'difficulty'=>$this->difficulty->getName(),
            'category'=>$this->category,
            'timePerTurn'=>$this->timePerTurn
        ];
    }
    public static function fromSerializable(array $data) {
        $diffName = $data['difficulty'];
        $diff = $diffName === 'easy' ? new Easy() : ($diffName === 'medium' ? new Medium() : new Hard());
        $game = new Game($diff, $data['category'], [$data['players'][0]['name'],$data['players'][1]['name']], __DIR__ . '/../memes');
        $game->deck = Deck::fromSerializable($data['deck']);
        $game->players = [];
        foreach ($data['players'] as $p) {
            $player = new Player($p['name'], $data['timePerTurn']);
            for ($i=0;$i<$p['score'];$i++) $player->incrementScore(1);
            $player->setTimeLeft($p['time']);
            $game->players[] = $player;
        }
        $game->currentPlayer = $data['current'];
        return $game;
    }
}
