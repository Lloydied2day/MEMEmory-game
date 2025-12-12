<?php
require_once __DIR__ . '/Difficulty.php';
class Medium extends Difficulty {
    public function getPairs(): int { return 8; }
    public function getName(): string { return 'medium'; }
}
