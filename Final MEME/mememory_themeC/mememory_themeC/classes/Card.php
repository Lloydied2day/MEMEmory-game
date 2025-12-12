<?php
class Card {
    private $id; private $image; private $matched=false; private $index;
    public function __construct(int $id, string $image, int $index) {
        $this->id=$id; $this->image=$image; $this->index=$index;
    }
    public function getId(): int { return $this->id; }
    public function getImage(): string { return $this->image; }
    public function isMatched(): bool { return $this->matched; }
    public function setMatched(bool $m=true){ $this->matched=$m; }
    public function getIndex(): int { return $this->index; }
}
