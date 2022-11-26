<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\session;

use phuongaz\giftcode\Loader;
use pocketmine\player\Player;

class Session {

        private Player $player;
        private array $codes = [];

        public function __construct(Player $player){
            $this->player = $player;
        }

        public function addCode(string $code): void{
            $this->codes[] = $code;
        }

        public function removeCode(string $code): void{
            unset($this->codes[$code]);
        }

        public function getPlayer(): Player{
            return $this->player;
        }

        public function getCodes(): array{
            return $this->codes;
        }

        public function save(): void{
            $database = Loader::getInstance()->getProvider();
            $database->fromSession($this);
        }

        public function hasCode(string $code): bool{
            return in_array($code, $this->codes);
        }

}