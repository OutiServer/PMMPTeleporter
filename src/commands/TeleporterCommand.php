<?php

declare(strict_types=1);

namespace outiserver\teleporter\commands;

use CortexPE\Commando\BaseCommand;
use outiserver\teleporter\commands\subcommands\AddSubCommand;
use outiserver\teleporter\commands\subcommands\RemoveSubCommand;
use outiserver\teleporter\commands\subcommands\TeleportSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TeleporterCommand extends BaseCommand
{
    protected function prepare(): void
    {
        $this->setPermission("teleporter.command");
        $this->registerSubCommand(new AddSubCommand("add", "テレポート地点を追加する"));
        $this->registerSubCommand(new RemoveSubCommand("remove", "テレポート地点を削除する"));
        $this->registerSubCommand(new TeleportSubCommand("teleport", "テレポーターを起動する", ["tp"]));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $sender->sendMessage(TextFormat::GREEN . "Teleporter Commands");
        foreach ($this->getSubCommands() as $subCommand) {
            if ($subCommand->testPermissionSilent($sender)) {
                $sender->sendMessage(TextFormat::GREEN . $subCommand->getUsageMessage());
            }
            else {
                $sender->sendMessage(TextFormat::RED . $subCommand->getUsageMessage());
            }
        }
    }
}