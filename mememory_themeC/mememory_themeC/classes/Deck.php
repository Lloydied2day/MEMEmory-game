<?php
require_once __DIR__ . '/Card.php';
class Deck {
    private $cards=[];
    public function generateFromFolder(string $folder, int $pairs) {
        $files = array_values(array_filter(scandir($folder), function($f) use ($folder) {
            return !is_dir($folder . DIRECTORY_SEPARATOR . $f) && preg_match('/\.(jpe?g|png|gif)$/i', $f);
        }));
        if (count($files) < $pairs) {
            throw new Exception("Not enough images in $folder (need $pairs).");
        }
        shuffle($files);
        $chosen = array_slice($files, 0, $pairs);
        $tmp=[]; $idx=0;
        foreach ($chosen as $i=>$file) {
            $id=$i+1;
            $tmp[] = new Card($id, $folder . '/' . $file, $idx++);
            $tmp[] = new Card($id, $folder . '/' . $file, $idx++);
        }
        shuffle($tmp);
        $this->cards = array_values($tmp);
    }
    public function getCards(): array { return $this->cards; }
    public function setMatchedByIndex(int $index) {
        foreach ($this->cards as $c) if ($c->getIndex() === $index) { $c->setMatched(true); break; }
    }
    public function getCardByIndex(int $index): ?Card {
        foreach ($this->cards as $c) if ($c->getIndex() === $index) return $c;
        return null;
    }
    public function allMatched(): bool {
        foreach ($this->cards as $c) if (!$c->isMatched()) return false;
        return true;
    }
    public function toSerializable(): array {
        return array_map(function($c){ return ['id'=>$c->getId(),'img'=>$c->getImage(),'matched'=>$c->isMatched(),'index'=>$c->getIndex()]; }, $this->cards);
    }
    public static function fromSerializable(array $data) {
        $deck = new Deck(); $cards=[];
        foreach ($data as $d) {
            $card = new Card($d['id'], $d['img'], $d['index']);
            if ($d['matched']) $card->setMatched(true);
            $cards[] = $card;
        }
        $deck->cards = $cards; return $deck;
    }
}
