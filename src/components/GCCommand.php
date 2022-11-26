<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components;

use phuongaz\giftcode\components\form\CreateCode;
use phuongaz\giftcode\components\form\GiveCode;
use phuongaz\giftcode\components\form\ListCodes;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class GCCommand extends Command
{
    public function __construct()
    {
        parent::__construct("giftcode", "", "");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if(!($sender instanceof Player)){
            $sender->sendMessage("§cYou must be a player to use this command!");
            return true;
        }
        if(Server::getInstance()->isOp($sender->getName())) {
            if(isset($args[0])) {
                $form = match($args[0]) {
                  "list" => new ListCodes(isset($args[1]) ? $sender->getServer()->getPlayerExact($args[1]) : $sender),
                  "create" => new CreateCode(),
                  "give" => new GiveCode(),
                };
                $sender->sendForm($form);
                return true;
            }
        }
        $sender->sendForm(new ListCodes($sender));
        return true;
    }
}