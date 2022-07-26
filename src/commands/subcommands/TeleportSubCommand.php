<?php

declare(strict_types=1);

namespace outiserver\teleporter\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use outiserver\teleporter\forms\SelectTeleporterForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TeleportSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("teleporter.teleport.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("[Teleporter] " . TextFormat::RED . "このコマンドはサーバー内で実行してください");
            return;
        }

        (new SelectTeleporterForm())->execute($sender);
    }
}