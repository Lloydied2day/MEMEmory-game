<?php
abstract class Difficulty {
    abstract public function getPairs(): int;
    abstract public function getName(): string;
    public function getTimePerTurn(): int { return 15; }
}
