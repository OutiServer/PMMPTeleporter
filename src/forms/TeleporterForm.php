<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms;

use Ken_Cir\LibFormAPI\FormContents\SimpleForm\SimpleFormButton;
use Ken_Cir\LibFormAPI\Forms\SimpleForm;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\forms\admin\AddTeleporterForm;
use outiserver\teleporter\forms\admin\RemoveTeleporterForm;
use outiserver\teleporter\language\LanguageManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;
use pocketmine\Server;

class TeleporterForm implements BaseForm
{
    public const FORM_KEY = "teleporter_form";

    public function execute(Player $player): void
    {
        $contents = [
            new SimpleFormButton(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.teleporter.button1"))
        ];

        if (Server::getInstance()->isOp($player->getName())) {
            $contents[] = new SimpleFormButton(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.teleporter.button2"));
            $contents[] = new SimpleFormButton(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.teleporter.button3"));
        }
        $form = new SimpleForm(Teleporter::getInstance(),
            $player,
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.teleporter.title"),
            "",
            $contents,
            function (Player $player, int $data) {
                if ($data === 0) {
                    (new SelectTeleporterForm())->execute($player);
                } elseif ($data === 1) {
                    (new AddTeleporterForm())->execute($player);
                } elseif ($data === 2) {
                    (new RemoveTeleporterForm())->execute($player);
                }
            },
            function (Player $player): void {
                Teleporter::getInstance()->getStackFormManager()->deleteStack($player->getXuid());
            });

        Teleporter::getInstance()->getStackFormManager()->addStackForm($player->getXuid(), self::FORM_KEY, $form);
    }
}