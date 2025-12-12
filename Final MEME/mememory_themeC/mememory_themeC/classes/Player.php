<?php
class Player {
    private $name; private $score=0; private $timeLeft;
    public function __construct(string $name, int $timeLeft = 15) { $this->name=$name; $this->timeLeft=$timeLeft; }
    public function getName(): string { return $this->name; }
    public function getScore(): int { return $this->score; }
    public function incrementScore(int $n=1){ $this->score += $n; }
    public function getTimeLeft(): int { return $this->timeLeft; }
    public function setTimeLeft(int $t){ $this->timeLeft = $t; }
}
