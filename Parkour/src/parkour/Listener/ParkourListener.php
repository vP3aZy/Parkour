<?php

namespace parkour\Listener;

use parkour\ParkourMain;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\Position;
use function print_r;
use function var_dump;

class ParkourListener implements Listener {

    public function onEnterCheckpoint(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $arena = null;

        foreach (ParkourMain::getArenaConfig()->getAll(true) as $arenas){
            if(ParkourMain::getArena($arenas)->getWorld()->getFolderName() === $player->getWorld()->getFolderName())
                $arena = $arenas;
        }

        if($arena !== null && ParkourMain::getCheckpoint($player->getPosition(), $arena)){
            $player->sendPopUp("§aYou reached a checkpoint!");
        }
    }
}