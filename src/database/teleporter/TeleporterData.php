<?php

declare(strict_types=1);

namespace outiserver\teleporter\database\teleporter;

use outiserver\economycore\Database\Base\BaseData;
use poggit\libasynql\DataConnector;

class TeleporterData extends BaseData
{
    private int $id;

    private string $worldName;

    private string $name;

    private int $x;

    private int $y;

    private int $z;

    public function __construct(DataConnector $dataConnector, int $id, string $worldName, string $name, int $x, int $y, int $z)
    {
        parent::__construct($dataConnector);

        $this->id = $id;
        $this->worldName = $worldName;
        $this->name = $name;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    protected function update(): void
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getWorldName(): string
    {
        return $this->worldName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getZ(): int
    {
        return $this->z;
    }
}