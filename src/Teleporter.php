<?php

declare(strict_types=1);

namespace outiserver\teleporter;

use Ken_Cir\LibFormAPI\FormStack\StackFormManager;
use outiserver\teleporter\commands\TeleporterCommand;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\language\LanguageManager;
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

    private LanguageManager $languageManager;

    private StackFormManager $stackFormManager;

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

        $this->languageManager = new LanguageManager("{$this->getFile()}resources/lang");

        $this->stackFormManager = new StackFormManager();

        $this->getServer()->getCommandMap()->registerAll($this->getName(),
        [
            new TeleporterCommand($this, "teleporter", "テレポーターコマンド", "/teleporter", [])
        ]);
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

    /**
     * @return StackFormManager
     */
    public function getStackFormManager(): StackFormManager
    {
        return $this->stackFormManager;
    }

    /**
     * @return LanguageManager
     */
    public function getLanguageManager(): LanguageManager
    {
        return $this->languageManager;
    }
}