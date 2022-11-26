<?php

declare(strict_types=1);

namespace phuongaz\giftcode;

use phuongaz\giftcode\components\event\PlayerUseCodeEvent;
use phuongaz\giftcode\utils\CodeUtils;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class EventHandler implements Listener
{
    public function onLogin(PlayerLoginEvent $event) :void
    {
        Loader::getInstance()->getProvider()->toSession($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event) :void
    {
        Loader::getInstance()->getSessionManager()->save($event->getPlayer());
    }

    public function onUseCode(PlayerUseCodeEvent $event) :void
    {
        $player = $event->getPlayer();
        $items = CodeUtils::getItems($event->getCode());
        $commands = CodeUtils::getCommands($event->getCode());
        if($items !== null){
            $inventory = $player->getInventory();
            foreach($items as $item){
                if($inventory->canAddItem($item)){
                    $inventory->addItem($item);
                }else{
                    $player->sendMessage("§cYou don't have enough space in your inventory!");
                    break;
                }
            }
        }
        $console = new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage());
        foreach($commands as $command){
            $player->getServer()->dispatchCommand($console, str_replace("{player}", $player->getName(), $command));
        }
    }

}