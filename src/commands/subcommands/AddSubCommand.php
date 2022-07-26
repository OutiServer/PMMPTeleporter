<?php

declare(strict_types=1);

namespace outiserver\teleporter\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use outiserver\teleporter\database\teleporter\TeleporterDataManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AddSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("teleporter.add.command");
        $this->registerArgument(0, new RawStringArgument("name"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("[Teleporter] " . TextFormat::RED . "このコマンドはサーバー内で実行してください");
            return;
        }

        $pos = $sender->getPosition();
        TeleporterDataManager::getInstance()->create($pos->getWorld()->getFolderName(),
        $args["name"],
        $pos->getFloorX(),
            $pos->getFloorY(),
        $pos->getFloorZ());

        $sender->sendMessage("[Teleporter] " . TextFormat::GREEN . "{$pos->getWorld()->getFolderName()}:{$pos->getFloorX()}:{$pos->getFloorY()}:{$pos->getFloorZ()}をテレポート地点に設定しました、地点名: {$args["name"]}");
    }
}