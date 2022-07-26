<?php

declare(strict_types=1);

namespace outiserver\teleporter;

use outiserver\teleporter\commands\TeleporterCommand;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Teleporter extends PluginBase
{
    use SingletonTrait;

    private DataConnector $dataConnector;

    private TeleporterDataManager $teleporterDataManager;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveResource("database.yml", true);

        $this->dataConnector = libasynql::create($this, (new Config("{$this->getDataFolder()}database.yml", Config::YAML))->get("database"), [
            "sqlite" => "sqlite.sql"
        ]);
        $this->dataConnector->executeGeneric("economy.teleporter.teleporters.init");
        $this->dataConnector->waitAll();

        $this->teleporterDataManager = new TeleporterDataManager($this->dataConnector);

        $this->getServer()->getCommandMap()->register($this->getName(), new TeleporterCommand($this, "teleporter", "テレポーターコマンド", ["tpr"]));
    }

    /**
     * @return DataConnector
     */
    public function getDataConnector(): DataConnector
    {
        return $this->dataConnector;
    }

    /**
     * @return TeleporterDataManager
     */
    public function getTeleporterDataManager(): TeleporterDataManager
    {
        return $this->teleporterDataManager;
    }
}