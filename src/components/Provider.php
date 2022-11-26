<?php

declare(strict_types=1);

namespace phuongaz\giftcode\components;

use phuongaz\giftcode\components\session\Session;
use pocketmine\player\Player;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use poggit\libasynql\DataConnector;

class Provider{

    CONST CREATE_TABLE = "table.init";
    CONST INSERT_DATA = "table.insert";
    CONST SELECT_DATA = "table.select";
    const DELETE_DATA = "table.delete";
    const UPDATE_DATA = "table.update";

    private DataConnector $dataConnector;

    public function __construct(DataConnector $dataConnector){
        $this->dataConnector = $dataConnector;
        $this->dataConnector->executeGeneric(self::CREATE_TABLE);
    }

    public function insertCodes(Player $player, array $codes) :void
    {
        $this->dataConnector->executeInsert(self::INSERT_DATA, [
            "uuid" => $player->getUniqueId()->toString(),
            "code" => json_encode($codes),
        ]);
    }

    public function getCodesByPlayer(Player $player, \Closure $onSusses = null) :Promise{
        $resolver = new PromiseResolver();
        $this->dataConnector->executeSelect(self::SELECT_DATA, [
            "uuid" => $player->getUniqueId()->toString()
        ], $onSusses ?? function(array $data) use ($resolver){
            $resolver->resolve($data);
        });
        return $resolver->getPromise();
    }

    public function updateCodes(Player $player, array $codes) :void
    {
        $this->dataConnector->executeChange(self::UPDATE_DATA, [
            "uuid" => $player->getUniqueId()->toString(),
            "code" => json_encode($codes),
        ], fn() => null, function(?array $data) use ($player, $codes){
            $this->insertCodes($player, $codes);
        });
    }


    public function toSession(Player $player) :void{
        $this->getCodesByPlayer($player, function(array $data) use ($player){
            if(empty($data)) return;
            $codes = [];
            $session = new Session($player);
            foreach($data as $code){
                $session->addCode($code);
                $codes[] = $code;
            }
            if(count($codes) > 0){
                $player->sendMessage("§aYou have " . count($codes) . " giftcodes, /giftcode list to see its");
            }
        });
    }

    public function fromSession(Session $session) :void{
        $this->updateCodes($session->getPlayer(), $session->getCodes());
    }

}