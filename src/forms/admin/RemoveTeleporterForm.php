<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms\admin;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use Ken_Cir\LibFormAPI\Utils\FormUtil;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\database\teleporter\TeleporterData;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\language\LanguageManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;

class RemoveTeleporterForm implements BaseForm
{
    public const FORM_KEY = "remove_teleporter_form";

    public function execute(Player $player): void
    {
        $teleporters = TeleporterDataManager::getInstance()->getAll(true);
        if (count($teleporters) < 1) {
            $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.error"));
            return;
        }

        $teleporterButtons = array_map(function (TeleporterData $teleporterData) {
            return new SimpleFormButton("{$teleporterData->getName()} {$teleporterData->getWorldName()}:{$teleporterData->getX()}:{$teleporterData->getY()}:{$teleporterData->getZ()}");
        }, $teleporters);

        $form = new SimpleForm(Teleporter::getInstance(),
        $player,
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.remove_teleporter.title"),
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.remove_teleporter.content"),
        $teleporterButtons,
        function (Player $player, int $data) use ($teleporters) {
            $teleporter = $teleporters[$data];
            TeleporterDataManager::getInstance()->delete($teleporter->getId());
            $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.remove_teleporter.success", [$teleporter->getName()]));

            Teleporter::getInstance()->getStackFormManager()->deleteStackForm($player->getXuid(), self::FORM_KEY);
            FormUtil::backForm(Teleporter::getInstance(), [$this, "execute"], [$player], 3);
        },
        function (Player $player) {
            Teleporter::getInstance()->getStackFormManager()->deleteStackForm($player->getXuid(), self::FORM_KEY);
            Teleporter::getInstance()->getStackFormManager()->getStackFormEnd($player->getXuid())->reSend();
        });

        Teleporter::getInstance()->getStackFormManager()->addStackForm($player->getXuid(), self::FORM_KEY, $form);
    }
}