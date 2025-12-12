<?php
require_once __DIR__ . '/Difficulty.php';
class Easy extends Difficulty {
    public function getPairs(): int { return 3; }
    public function getName(): string { return 'easy'; }
}
