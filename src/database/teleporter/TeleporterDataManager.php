<?php

declare(strict_types=1);

namespace outiserver\teleporter\database\teleporter;

use outiserver\economycore\Database\Base\BaseAutoincrement;
use outiserver\economycore\Database\Base\BaseDataManager;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;

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
                    $this->seq = $data["seq"];
                }
            });
        $this->dataConnector->executeSelect("economy.teleporter.teleporters.load",
            [],
            function (array $row) {
                foreach ($row as $data) {
                    $this->data[$data["id"]] = new TeleporterData($this->dataConnector, $data["id"], $data["world_name"], $data["name"], $data["x"], $data["y"], $data["z"]);
                }
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
            ]);

        return ($this->data[++$this->seq] = new TeleporterData($this->dataConnector, $this->seq, $worldName, $name, $x, $y ,$z));
    }

    public function delete(int $id): void
    {
        if (!$this->get($id)) return;

        $this->dataConnector->executeGeneric(
            "economy.teleporter.teleporters.delete",
            [
                "id" => $id
            ]);
        unset($this->data[$id]);
    }
}