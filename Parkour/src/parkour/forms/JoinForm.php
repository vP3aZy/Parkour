<?php

namespace parkour\forms;

use jojoe77777\FormAPI\SimpleForm;
use parkour\ParkourMain;
use parkour\tasks\ParkourCountdown;
use pocketmine\player\Player;

class JoinForm {

    public static function openForm(Player $player) {
        $form = new SimpleForm(function (Player $player, $data = null){
            if($data === null) {
                return;
            }

            if (($loc = ParkourMain::getArena($data)) !== null) {
                $players = ParkourMain::getArena($data)->getWorld()->getPlayers();
                $players[] = $player;
                $player->teleport($loc);
                ParkourMain::getInstance()->getScheduler()->scheduleRepeatingTask(new ParkourCountdown($players), 20);
            } else {
                $player->sendMessage("§4Error!");
            }
        });
        $form->setTitle("§bParkours:");
        $form->setContent("Choose a Parkour, that you want to join!");
        foreach (ParkourMain::getArenaConfig()->getAll(true) as $c) {
            $form->addButton("§eName§7: §f" . $c, -1, "", $c);
        }
        $form->sendToPlayer($player);
    }
}