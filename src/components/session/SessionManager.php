<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\session;

use pocketmine\player\Player;

class SessionManager
{
    private array $sessions = [];

    public function getSession(Player $player): ?Session
    {
        $uuid = $player->getUniqueId()->toString();
        if(!isset($this->sessions[$uuid])){
            return null;
        }
        return $this->sessions[$uuid];
    }

    public function createSession(Player $player): Session
    {
        $uuid = $player->getUniqueId()->toString();
        if(isset($this->sessions[$uuid])){
            throw new \RuntimeException("Session already exists");
        }
        $this->sessions[$uuid] = new Session($player);
        return $this->sessions[$uuid];
    }

    public function removeSession(Player $player): void
    {
        $uuid = $player->getUniqueId()->toString();
        if(!isset($this->sessions[$uuid])){
            throw new \RuntimeException("Session does not exist");
        }
        unset($this->sessions[$uuid]);
    }

    public function getSessions(): array
    {
        return $this->sessions;
    }

    public function save(Player $player) :void {
        $uuid = $player->getUniqueId()->toString();
        if(isset($this->sessions[$uuid])){
            $this->sessions[$uuid]->save();
            unset($this->sessions[$uuid]);
        }
    }

    public function hasSession(Player $player) :bool {
        $uuid = $player->getUniqueId()->toString();
        return isset($this->sessions[$uuid]);
    }

}