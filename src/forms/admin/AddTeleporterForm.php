<?php

declare(strict_types=1);

namespace outiserver\teleporter\forms\admin;

use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentInput;
use Ken_Cir\LibFormAPI\FormContents\CustomForm\ContentLabel;
use Ken_Cir\LibFormAPI\Forms\CustomForm;
use Ken_Cir\LibFormAPI\Utils\FormUtil;
use outiserver\economycore\Forms\Base\BaseForm;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use outiserver\teleporter\language\LanguageManager;
use outiserver\teleporter\Teleporter;
use pocketmine\player\Player;

class AddTeleporterForm implements BaseForm
{
    public const FORM_KEY = "add_teleporter_form";

    public function execute(Player $player): void
    {
        $pos = $player->getPosition();
        $form = new CustomForm(Teleporter::getInstance(),
            $player,
            LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.add_teleporter.title"),
            [
                new ContentLabel(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.add_teleporter.label", [$pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ()])),
                new ContentInput(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.add_teleporter.input"))
            ],
            function (Player $player, array $data) {
                $pos = $player->getPosition();
                TeleporterDataManager::getInstance()->create($pos->getWorld()->getFolderName(),
                    $data[1],
                    $pos->getFloorX(),
                    $pos->getFloorY(),
                    $pos->getFloorZ());

                $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.add_teleporter.success", [$data[1]]));
                $player->sendMessage(LanguageManager::getInstance()->getLanguage($player->getLocale())->translateString("form.back"));
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