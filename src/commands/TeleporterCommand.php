<?php

declare(strict_types=1);

namespace outiserver\teleporter\commands;

use outiserver\teleporter\forms\TeleporterForm;
use outiserver\teleporter\language\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class TeleporterCommand extends Command implements PluginOwned
{
    private Plugin $plugin;

    public function __construct(Plugin $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);

        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(LanguageManager::getInstance()->getLanguage($sender->getLanguage()->getLang())->translateString("command.error.please_used_server"));
            return;
        }

        (new TeleporterForm())->execute($sender);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
}