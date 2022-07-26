<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\economycore\Database\Economy\EconomyDataManager;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\database\teleporter\TeleporterData;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;

class SelectTeleporterForm implements BaseForm
{
    public function execute(Player $player): void
    {
        $teleporters = TeleporterDataManager::getInstance()->getAll(true);
        if (count($teleporters) < 1) {
            $player->sendMessage("[Teleporter] " . TextFormat::RED . "テレポートできる場所が1つもないようです");
            return;
        }

        $teleporterButtons = array_map(function (TeleporterData $teleporterData) {
            return new SimpleFormButton($teleporterData->getName());
        }, $teleporters);

        (new SimpleForm(Teleporter::getInstance(),
        $player,
        "テレポーター",
        "テレポート先を選択してください",
        $teleporterButtons,
        function (Player $player, int $data) use ($teleporters) {
            $teleporterData = $teleporters[$data];
            $pos = new Position($teleporterData->getX(), $teleporterData->getY(), $teleporterData->getZ(), Server::getInstance()->getWorldManager()->getWorldByName($teleporterData->getWorldName()));
            $distance = (int)$pos->distance($player->getPosition());
            var_dump($distance);
            $economyData = EconomyDataManager::getInstance()->get($player->getXuid());
            if ($economyData->getMoney() < ($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1))) {
                $player->sendMessage("[Teleporter] " . TextFormat::RED . "テレポートするためのお金があと" . (($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1)) - $economyData->getMoney()) . "円足りません");
                return;
            }

            $economyData->removeMoney(($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1)));
            $player->teleport($pos);
            $player->sendMessage("[Teleporter] " . TextFormat::GREEN . ($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1)) .  "円使用してテレポートしました");
        }));
    }
}