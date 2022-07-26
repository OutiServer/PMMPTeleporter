<?php

declare(strict_types=1);

namespace outiserver\teleporter\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use outiserver\teleporter\forms\RemoveTeleporterForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RemoveSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("teleporter.remove.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("[Teleporter] " . TextFormat::RED . "このコマンドはサーバー内で実行してください");
            return;
        }

        (new RemoveTeleporterForm())->execute($sender);
    }
}