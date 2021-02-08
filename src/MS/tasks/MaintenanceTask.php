<?php


namespace MS\tasks;


use MS\MS;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class MaintenanceTask extends Task {

    public function onRun(int $currentTick) {
        if (MS::isMaintenance()) {
            foreach (Server::getInstance()->getLoggedInPlayers() as $onlinePlayer) {
                if ($onlinePlayer->hasPermission(MS::getCfg()->get("Maintenance-Join-bypass-permission"))) {
                    return;
                } else {
                    if (MS::getPlayerCfg()->exists($onlinePlayer->getName())) {
                        return;
                    } else {
                        $onlinePlayer->kick(MS::getCfg()->get("Maintenance-kick-reason"), false);
                    }
                }
            }
        }
    }
}