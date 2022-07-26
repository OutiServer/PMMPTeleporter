<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\database\teleporter\TeleporterData;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RemoveTeleporterForm implements BaseForm
{
    public function execute(Player $player): void
    {
        $teleporters = TeleporterDataManager::getInstance()->getAll(true);
        if (count($teleporters) < 1) {
            $player->sendMessage("[Teleporter] " . TextFormat::RED . "テレポートできる場所が1つもないようです");
            return;
        }

        $teleporterButtons = array_map(function (TeleporterData $teleporterData) {
            return new SimpleFormButton("{$teleporterData->getName()} {$teleporterData->getWorldName()}:{$teleporterData->getX()}:{$teleporterData->getY()}:{$teleporterData->getZ()}");
        }, $teleporters);

        (new SimpleForm(Teleporter::getInstance(),
        $player,
        "テレポーター削除",
        "削除するテレポーターを選択してください",
        $teleporterButtons,
        function (Player $player, int $data) use ($teleporters) {
            $teleporter = $teleporters[$data];
            TeleporterDataManager::getInstance()->delete($teleporter->getId());
            $player->sendMessage("[Teleporter] " . TextFormat::GREEN . "テレポーター {$teleporter->getName()} を削除しました");
        }));
    }
}