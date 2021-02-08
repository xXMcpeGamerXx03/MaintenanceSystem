<?php

namespace MS;

use MS\commands\MaintenanceCommand;
use MS\tasks\MaintenanceTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class MS extends PluginBase implements Listener {

    /** @var Config $config */
    private static $config;
    /** @var Config $playerConfig */
    private static $playerConfig;

    public static function setMaintenance(bool $value) {
        $cfg = self::$config;
        $cfg->set("Maintenance", $value);
        $cfg->save();

        if ($value == true) {
            Server::getInstance()->getNetwork()->setName(self::$config->get("MaintenanceMOTD"));
        } else {
            Server::getInstance()->getNetwork()->setName(self::$config->get("MOTD"));
        }
    }

    public static function getPlayers(): array {
        if (empty(self::getPlayerCfg()->getAll(true))) {
            return ["/"];
        } else {
            return self::getPlayerCfg()->getAll(true);
        }
    }

    public static function isMaintenance(): bool {
        return (boolean) self::$config->get("Maintenance");
    }

    public static function getCfg(): Config
    {
        return self::$config;
    }

    public static function getPlayerCfg(): Config
    {
        return self::$playerConfig;
    }

    public function onEnable() {
        $this->saveResource("config.yml");
        self::$config = new Config($this->getDataFolder() . "config.yml", 2);
        self::$playerConfig = new Config($this->getDataFolder() . "players.yml", 2);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new MaintenanceTask(), 20);
        $this->getServer()->getCommandMap()->register("wartung", new MaintenanceCommand(self::$config->get("Maintenance-Command-permission")));
        self::setMaintenance(self::isMaintenance());
    }
}