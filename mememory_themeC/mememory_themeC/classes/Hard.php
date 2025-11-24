<?php
require_once __DIR__ . '/Difficulty.php';
class Hard extends Difficulty {
    public function getPairs(): int { return 12; }
    public function getName(): string { return 'hard'; }
}
