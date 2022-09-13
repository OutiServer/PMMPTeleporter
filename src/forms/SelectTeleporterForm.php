<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\economycore\Database\Economy\EconomyDataManager;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\database\teleporter\TeleporterData;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\language\LanguageManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;

class SelectTeleporterForm implements BaseForm
{
    public const FORM_KEY = "select_teleporter_form";

    public function execute(Player $player): void
    {
        $teleporters = TeleporterDataManager::getInstance()->getAll(true);
        if (count($teleporters) < 1) {
            $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.error"));
            return;
        }

        $teleporterButtons = array_map(function (TeleporterData $teleporterData) {
            return new SimpleFormButton($teleporterData->getName());
        }, $teleporters);

        $form = new SimpleForm(Teleporter::getInstance(),
            $player,
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.title"),
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.content"),
            $teleporterButtons,
            function (Player $player, int $data) use ($teleporters) {
                $teleporterData = $teleporters[$data];
                $pos = new Position($teleporterData->getX(), $teleporterData->getY(), $teleporterData->getZ(), Server::getInstance()->getWorldManager()->getWorldByName($teleporterData->getWorldName()));
                $distance = (int)$pos->distance($player->getPosition());
                $economyData = EconomyDataManager::getInstance()->get($player->getXuid());
                if ($economyData->getMoney() < ($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1))) {
                    $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.no_money", [(($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1)) - $economyData->getMoney())]));
                    return;
                }

                $economyData->removeMoney(($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1)));
                $player->teleport($pos);
                $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.select_teleporter.success", [($distance * Teleporter::getInstance()->getConfig()->get("one_block_price", 1))]));

                Teleporter::getInstance()->getStackFormManager()->deleteStack($player->getXuid());
            },
            function (Player $player) {
                Teleporter::getInstance()->getStackFormManager()->deleteStackForm($player->getXuid(), self::FORM_KEY);
                Teleporter::getInstance()->getStackFormManager()->getStackFormEnd($player->getXuid())->reSend();
            });

        Teleporter::getInstance()->getStackFormManager()->addStackForm($player->getXuid(), self::FORM_KEY, $form);
    }
}