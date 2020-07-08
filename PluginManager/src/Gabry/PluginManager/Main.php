<?php

namespace Gabry\PluginManager;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase implements Listener {
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("This plugin is now enabled. If you have problems with this plugin, contact @ Discord (Gabry#6899).");
        if (!$this->getConfig()->check()) {
            $this->getLogger()->notice("Saving default config.yml file...");
            $this->saveDefaultConfig();
            $this->getLogger()->notice("Saved config.yml in " . $this->getConfig()->getPath());
        }

    }
    public function onDisable() {
        $this->getLogger()->info("This plugin is now disabled.");
    }

    public function sendUsage($sender) {
        $titlePrefix = $this->getConfig()->get("title-prefix");
        if ($titlePrefix) {
            $sender->sendMessage($titlePrefix . " " .  TextFormat::RED . "Usage: /pluginmanager <enable|disable|reload|reloadconfig> <pluginName>");
        }
        else {
            $sender->sendMessage(TextFormat::RED . "Usage: /pluginmanager <enable|disable|reload|reloadconfig> <pluginName>");
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        switch($command->getName()) {
            case "pluginmanager":
                if (!$sender->isOp()) {
                    $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
                    return true;
                }
                $pluginManager = $this->getServer()->getPluginManager();
                $titlePrefix = $this->getConfig()->get("title-prefix");
                if (!isset($args[0])) {
                    $this->sendUsage($sender);
                    return true;
                }
                if ($args[0] === "reloadconfig") {
                    $this->reloadConfig();
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::GREEN . TextFormat::GREEN . "The configuration files were been reloaded.");
                    }
                    else {
                        $sender->sendMessage(TextFormat::GREEN . "The configuration files were been reloaded.");
                    }
                    return true;
                }
                if ($args[0] === "help") {
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::AQUA . "Here are the list of available commands:");
                        $sender->sendMessage($titlePrefix . " " . TextFormat::WHITE . "/pluginmanager enable <pluginName> - Enables a plugin.");
                        $sender->sendMessage($titlePrefix . " " . TextFormat::WHITE . "/pluginmanager disable <pluginName> - Disables a plugin.");
                        $sender->sendMessage($titlePrefix . " " . TextFormat::WHITE . "/pluginmanager reload <pluginName> - Reloads a plugin.");
                        $sender->sendMessage($titlePrefix . " " . TextFormat::WHITE . "/pluginmanager reloadconfig - Reloads this plugin.");
                    }
                    else {
                        $sender->sendMessage(TextFormat::AQUA . "Here are the list of available commands:");
                        $sender->sendMessage("/pluginmanager enable <pluginName> - Enables a plugin.");
                        $sender->sendMessage("/pluginmanager disable <pluginName> - Disables a plugin.");
                        $sender->sendMessage("/pluginmanager reload <pluginName> - Reloads a plugin.");
                        $sender->sendMessage("/pluginmanager reloadconfig - Reloads this plugin.");
                    }
                    return true;
                }
                if (!isset($args[1])) {
                    if ($titlePrefix) {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::RED . "Please provide a plugin name.");
                    }
                    else {
                        $sender->sendMessage(TextFormat::RED . "Please provide a plugin name.");
                    }
                    return true;
                }
                if ($args[1] === "PluginManager") {
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::RED . "You can't manage that plugin!");
                    }
                    else {
                        $sender->sendMessage(TextFormat::RED . "You can't manage that plugin!");
                    }
                    return true;
                }
                if (!$pluginManager->getPlugin($args[1])) {
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::RED . "Plugin " . $args[1] . " does not exist!");
                    }
                    else {
                        $sender->sendMessage(TextFormat::RED . "Plugin " . $args[1] . " does not exist!");
                    }
                    return true;
                }
                if ($args[0] === "enable") {
                    if ($pluginManager->isPluginEnabled($pluginManager->getPlugin($args[1]))) {
                        if ($titlePrefix !== "") {
                            $sender->sendMessage($titlePrefix . " " . TextFormat::RED . "Plugin " . $args[1] . " is already enabled!");
                        }
                        else {
                            $sender->sendMessage(TextFormat::RED . "Plugin " . $args[1] . " is already enabled!");
                        }

                        return true;
                    }
                    $pluginManager->enablePlugin($pluginManager->getPlugin($args[1]));
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::GREEN . "Plugin " . $args[1] . " is enabled and running.");
                    }
                    else {
                        $sender->sendMessage(TextFormat::GREEN . "Plugin " . $args[1] . " is enabled and running.");
                    }
                    return true;
                }
                if ($args[0] === "disable") {
                    if (!$pluginManager->isPluginEnabled($pluginManager->getPlugin($args[1]))) {
                        if ($titlePrefix !== "") {
                            $sender->sendMessage($titlePrefix . " " . TextFormat::RED . "Plugin " . $args[1] . " is already disabled!");
                        }
                        else {
                            $sender->sendMessage(TextFormat::RED . "Plugin " . $args[1] . " is already disabled!");
                        }
                        return true;
                    }
                    $pluginManager->disablePlugin($pluginManager->getPlugin($args[1]));
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::GREEN . "Plugin " . $args[1] . " is disabled.");
                    }
                    else {
                        $sender->sendMessage(TextFormat::GREEN . "Plugin " . $args[1] . " is disabled.");
                    }
                    return true;
                }
                if ($args[0] === "reload") {
                    $pluginManager->disablePlugin($pluginManager->getPlugin($args[1]));
                    $pluginManager->enablePlugin($pluginManager->getPlugin($args[1]));
                    if ($titlePrefix !== "") {
                        $sender->sendMessage($titlePrefix . " " . TextFormat::GREEN . TextFormat::GREEN . "Plugin " . $args[1] . " has been reloaded.");
                    } else {
                        $sender->sendMessage(TextFormat::GREEN . "Plugin " . $args[1] . " has been reloaded.");
                    }
                    return true;

                }
                $this->sendUsage($sender);
                break;
        }
        return true;
    }
}
