<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\form;

use jojoe77777\FormAPI\CustomForm;
use phuongaz\giftcode\components\event\PlayerUseCodeEvent;
use phuongaz\giftcode\Loader;
use phuongaz\giftcode\utils\CodeUtils;
use phuongaz\giftcode\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;

class ListCodes extends CustomForm
{

    public function __construct(Player $player)
    {
        parent::__construct($this->getCallable());
        $sessionManager = Loader::getInstance()->getSessionManager();
        if($sessionManager->getSession($player) == null)
        {
            $sessionManager->createSession($player);
        }
        $codes = Loader::getInstance()->getSessionManager()->getSession($player)->getCodes();
        $this->setTitle("Giftcode");
        $this->addInput("§7Enter code to use", "");
        $this->addLabel("§7You have " . count($codes) . " giftcode(s)");
        $index = 0;
        foreach($codes as $code)
        {
            $index++;
            $this->addLabel("{$index}. {$code}");
        }
    }

    public function getCallable(): ?callable
    {
        return function(Player $player, ?array $data) :void
        {
            if(!isset($data[0])) return;
            $code = $data[0];
            $session = Loader::getInstance()->getSessionManager()->getSession($player);
            if(!$session->hasCode($code))
            {
                $player->sendMessage("§cYou don't have this code <{$code}>");
                return;
            }
            if(!CodeUtils::exists($code))
            {
                $player->sendMessage("§cThis code <{$code}> is not exists");
                return;
            }
            if(Utils::getTimeLeft($code) <= 0)
            {
                $player->sendMessage("§cGiftcode is expired");
                $session->removeCode($code);
                return;
            }
            (new PlayerUseCodeEvent($player, $code))->call();
            $session->removeCode($code);
            $session->save();
            Server::getInstance()->broadcastMessage("§a{$player->getName()} used giftcode <{$code}>");
            Utils::sendToast($player, "§aYou used giftcode <{$code}>", "§7You have " . count($session->getCodes()) . " giftcode(s) not used yet");
        };
    }
}