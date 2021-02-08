<?php


namespace MS\commands;


use MS\MS;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use function Composer\Autoload\includeFile;

class MaintenanceCommand extends Command {

    public function __construct(string $permission) {
        $this->setPermission($permission);
        parent::__construct("maintenance", "Maintenance Command", "", ["wartung"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender->hasPermission($this->getPermission())) {
            if (isset($args[0])) {
                if (strtolower($args[0]) == "toggle") {
                    if (MS::isMaintenance()) {
                        $sender->sendMessage(MS::getCfg()->get("Maintenance-Off-msg"));
                        MS::setMaintenance(false);
                    } else {
                        $sender->sendMessage(MS::getCfg()->get("Maintenance-On-msg"));
                        MS::setMaintenance(true);
                    }
                } else if (strtolower($args[0]) == "add") {
                    if (isset($args[1])) {
                        if (MS::getPlayerCfg()->exists($args[1])) {
                            $sender->sendMessage(str_replace("{player}", $args[1], MS::getCfg()->get("Player-Already-On-List")));
                        } else {
                            $sender->sendMessage(str_replace("{player}", $args[1], MS::getCfg()->get("Maintenance-AddedPlayer-msg")));
                            $cfg = MS::getPlayerCfg();
                            $cfg->set($args[1], true);
                            $cfg->save();
                        }
                    } else {
                        $sender->sendMessage(MS::getCfg()->get("No-Arguments-message"));
                    }
                } else if (strtolower($args[0]) == "remove") {
                    if (isset($args[1])) {
                        if (!MS::getPlayerCfg()->exists($args[1])) {
                            $sender->sendMessage(str_replace("{player}", $args[1], MS::getCfg()->get("Player-Not-On-List")));
                        } else {
                            $sender->sendMessage(str_replace("{player}", $args[1], MS::getCfg()->get("Maintenance-RemovedPlayer-msg")));
                            $cfg = MS::getPlayerCfg();
                            $cfg->remove($args[1]);
                            $cfg->save();
                        }
                    } else {
                        $sender->sendMessage(MS::getCfg()->get("No-Arguments-message"));
                    }
                } else if (strtolower($args[0]) == "list") {
                    $list = array();
                    foreach (MS::getPlayers() as $players) {
                        array_push($list, $players);
                    }

                    $sender->sendMessage(MS::getCfg()->get("List-message") . implode(", ", $list));
                } else {
                    $sender->sendMessage(MS::getCfg()->get("No-Arguments-message"));
                }
            } else {
                $sender->sendMessage(MS::getCfg()->get("No-Arguments-message"));
            }
        } else {
            $sender->sendMessage(MS::getCfg()->get("No-Perms-message"));
        }
    }
}