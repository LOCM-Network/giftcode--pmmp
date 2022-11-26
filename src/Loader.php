<?php

declare(strict_types=1);

namespace phuongaz\giftcode;

use phuongaz\giftcode\components\Provider;
use phuongaz\giftcode\components\GCCommand;
use phuongaz\giftcode\components\session\SessionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Loader extends PluginBase
{
    use SingletonTrait;

    private DataConnector $dataConnector;
    private SessionManager $session;
    private Config $codesConfig;

    protected function onLoad(): void
    {
       self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->codesConfig = new Config($this->getDataFolder() . "codes.yml", Config::YAML);
        $this->saveDefaultConfig();
        $this->dataConnector = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);
        $this->session = new SessionManager();
        $this->getServer()->getCommandMap()->register("giftcode", new GCCommand());
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
    }

    public function getProvider(): Provider {
        return new Provider($this->dataConnector);
    }

    public function getSessionManager(): SessionManager {
        return $this->session;
    }

    public function getCodesConfig(): Config {
        return $this->codesConfig;
    }

    protected function onDisable() :void
    {
        if(isset($this->dataConnector)) $this->dataConnector->close();
    }

}