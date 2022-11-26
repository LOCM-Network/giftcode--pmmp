<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\event;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerUseCodeEvent extends PlayerEvent
{
    private string $code;

    public function __construct(Player $player, string $code)
    {
        $this->player = $player;
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}