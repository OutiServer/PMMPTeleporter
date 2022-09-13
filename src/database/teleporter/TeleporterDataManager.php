<?php

declare(strict_types=1);

namespace outiserver\teleporter\database\teleporter;

use outiserver\economycore\Database\Base\BaseAutoincrement;
use outiserver\economycore\Database\Base\BaseDataManager;
use outiserver\teleporter\Teleporter;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\SqlError;

class TeleporterDataManager extends BaseDataManager
{
    use SingletonTrait;
    use BaseAutoincrement;

    public function __construct(DataConnector $dataConnector)
    {
        parent::__construct($dataConnector);
        self::setInstance($this);

        $this->dataConnector->executeSelect(
            "economy.teleporter.teleporters.seq",
            [],
            function (array $row) {
                if (count($row) < 1) {
                    $this->seq = 0;
                    return;
                }
                foreach ($row as $data) {
                    if (Teleporter::getInstance()->getDatabaseConfig()["type"] === "sqlite" or Teleporter::getInstance()->getDatabaseConfig()["type"] === "sqlite3" or Teleporter::getInstance()->getDatabaseConfig()["type"] === "sq3") {
                        $this->seq = $data["seq"];
                    } elseif (Teleporter::getInstance()->getDatabaseConfig()["type"] === "mysql" or Teleporter::getInstance()->getDatabaseConfig()["type"] === "mysqli") {
                        $this->seq = $data["Auto_increment"];
                    }
                }
            },
            function (SqlError $error) {
                Teleporter::getInstance()->getLogger()->error("[SqlError] {$error->getErrorMessage()}");
            });
        $this->dataConnector->executeSelect("economy.teleporter.teleporters.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new TeleporterData($this->dataConnector, $data["id"], $data["world_name"], $data["name"], $data["x"], $data["y"], $data["z"]);
                }
            },
            function (SqlError $error) {
                Teleporter::getInstance()->getLogger()->error("[SqlError] {$error->getErrorMessage()}");
            });
    }

    public function get(int $id): ?TeleporterData
    {
        if (!isset($this->data[$id])) return null;
        return $this->data[$id];
    }

    public function create(string $worldName, string $name, int $x, int $y, int $z): TeleporterData
    {
        $this->dataConnector->executeInsert("economy.teleporter.teleporters.create",
            [
                "world_name" => $worldName,
                "name" => $name,
                "x" => $x,
                "y" => $y,
                "z" => $z
            ],
            null,
            function (SqlError $error) {
                Teleporter::getInstance()->getLogger()->error("[SqlError] {$error->getErrorMessage()}");
            });

        return ($this->data[++$this->seq] = new TeleporterData($this->dataConnector, $this->seq, $worldName, $name, $x, $y, $z));
    }

    public function delete(int $id): void
    {
        if (!$this->get($id)) return;

        $this->dataConnector->executeGeneric(
            "economy.teleporter.teleporters.delete",
            [
                "id" => $id
            ],
            null,
            function (SqlError $error) {
                Teleporter::getInstance()->getLogger()->error("[SqlError] {$error->getErrorMessage()}");
            });
        unset($this->data[$id]);
    }
}