<?php

namespace parkour\commands;

use parkour\forms\JoinForm;
use parkour\ParkourMain;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ParkourCommand extends Command {

    public function __construct(){
        parent::__construct("parkour", "Parkour Main Command!", "/parkour help", ["pk"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        $player = $sender;

        if(!isset($args[0])) {
            $player->sendMessage("§7Usage: §c/parkour help");
            return;
        }

        switch ($args[0]) {
            case "help":
                $player->sendMessage("§fParkour commands:\n" . "§f/parkour help §7(§aShow a list with help commands!§7)\n" . "§f/parkour create §7(§aCreate a Parkour§7)\n" . "§f/parkour join §7(§aJoin a Parkour§7)\n" . "§f/parkour list §7(§aShow you all Parkours§7)\n" . "§f/parkour setcheckpoint §7(§aSet a Checkpoint!)\n" . "§f/parkour delete §7(§aDelete a Parkour§7)");
                break;

            case "create":
                if(!$player->hasPermission("parkour.cmd.create")){
                    $player->sendMessage("§4No perms!");
                    break;
                }
                if(!isset($args[1])){
                    $player->sendMessage("§7Usage: §c/parkour create (name)");
                    return;
                }
                if(ParkourMain::setSpawns($player, $args[1])) {
                    $player->sendMessage("§aThe parkour §e" . $args[1] . " §awas created!");
                    break;
                }

            case "join":
                if(!$player->hasPermission("parkour.cmd.join")) {
                    $player->sendMessage("§4No perms!");
                    return;
                }
                JoinForm::openForm($player);
                break;

            case "list":
                if(!$player->hasPermission("parkour.cmd.list")) {
                    $player->sendMessage("§4No perms!");
                    return;
                }
                $player->sendMessage("§aParkours§7: §f" . implode("; ", ParkourMain::getArenaConfig()->getAll(true)));
                break;

            case "delete":
                if(!$player->hasPermission("parkour.cmd.delete")) {
                    $player->sendMessage("§4No perms!");
                    return;
                }
                if(!isset($args[1])) {
                    $player->sendMessage("§7Usage: §c/parkour delete (name)");
                    return;
                }
                if(ParkourMain::deleteSpawns($player, $args[1])) {
                    $player->sendMessage("§cThe parkour §e" . $args[1] . " §cwas deleted!");
                    break;
                }

            case "setcheckpoint":
                if(!$player->hasPermission("parkour.cmd.checkpoint")){
                    $player->sendMessage("§4No perms!");
                    return;
                }
                if(!isset($args[1])){
                    $player->sendMessage("§7Usage: §c/parkour setcheckpoint (arenaname)");
                    return;
                }
                if(ParkourMain::getArena($args[1]) == null) {
                    $player->sendMessage("§4This parkour does not exists!");
                    return;
                }
                if($player->getWorld()->getFolderName() === ParkourMain::getArena($args[1])->getWorld()->getFolderName()){
                    ParkourMain::setCheckpoint($player, $args[1]);
                    $player->sendMessage("§aThe checkpoint was successfully placed!");
                    break;
                }
        }
    }
}