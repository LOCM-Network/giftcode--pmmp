<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components\form;

use jojoe77777\FormAPI\CustomForm;
use phuongaz\giftcode\Loader;
use phuongaz\giftcode\utils\Utils;
use pocketmine\player\Player;

class CreateCode extends CustomForm
{
    public function __construct()
    {
        parent::__construct($this->getCallable());
        $this->setTitle("Giftcode");
        $this->addLabel("§7Create giftcode");
        $this->addInput("§7Enter code", "", Utils::randomString("LOCM", 8));
        $this->addInput("§7Enter temp", "", "1d 4h");
        $this->addToggle("Rewards inventory (reward all items in your inventory)", false);
        $this->addInput("Commands {player}", "", "command1;command2;command3");
    }

    public function getCallable(): ?callable
    {
        return function(Player $player, ?array $data) :void {
            $codesConfig = Loader::getInstance()->getCodesConfig();
            if(isset($data[1])){
                $code = $data[1];
                $temp = $data[2] ?? "1d 4h";
                $rewardInventory = $data[3];
                $player->sendMessage("§aGiftcode created");
                $items = [];
                if($rewardInventory){
                    foreach($player->getInventory()->getContents() as $item){
                        $items[] = Utils::serializeItem($item);
                    }
                }
                $codesConfig->set($code, [
                    "temp" => Utils::parseTimeFormat($temp),
                    "rewardInventory" => $rewardInventory,
                    "items" => json_encode($items),
                    "commands" => json_encode(explode(";", $data[4]) ?? []),
                ]);
                $codesConfig->save();
            }
        };
    }
}