<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\form;


use jojoe77777\FormAPI\CustomForm;
use phuongaz\giftcode\Loader;
use phuongaz\giftcode\utils\CodeUtils;
use phuongaz\giftcode\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;

class GiveCode extends CustomForm
{

    private array $players = [];

    public function __construct(){
        parent::__construct($this->getCallable());
        $this->setTitle("Giftcode");
        $this->addDropdown("Code", CodeUtils::getCodes());
        foreach(Server::getInstance()->getOnlinePlayers() as $player)
        {
            $this->players[] = $player->getName();
        }
        $this->addDropdown("Player", $this->players);
    }

    public function getCallable(): ?callable
    {
        return function(Player $player, ?array $data) :void {
            if (!isset($data[0])) return;
            $code = CodeUtils::getCodes()[$data[0]];
            $target = Server::getInstance()->getPlayerExact($this->players[$data[1]]);
            $sessionManager = Loader::getInstance()->getSessionManager();
            if(!$sessionManager->hasSession($target)){
                $sessionManager->createSession($player);
            }
            if(($session = $sessionManager->getSession($target)) != null) {
                $session->addCode($code);
                Utils::sendToast($target, "§aYou received a new giftcode <{$code}>", "/giftcode to use it");
            }
        };
    }
}