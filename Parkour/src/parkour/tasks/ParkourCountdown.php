<?php

namespace parkour\tasks;

use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ParkourCountdown extends Task {

    private int $countdown = 10;

    /**
     * @param Player[] $p
     */
    public function __construct(private array $p) {}

    public function onRun(): void {
        if ($this->countdown == 0) {
            foreach ($this->p as $player) {
                $player->sendMessage("§aThe Parkour has started!");
                $player->sendTitle("§aGO!");
                $player->setImmobile(false);
            }
            $this->getHandler()->cancel();
            return;
        }
        if ($this->countdown == 10) {
            foreach ($this->p as $player) {
                $player->setImmobile();
                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 10 * 20));
            }
        }
        $color = TextFormat::RED;
        if ($this->countdown <= 5) $color = TextFormat::YELLOW;
     foreach ($this->p as $player) {
        $player->sendTitle($color . $this->countdown);
       }
        $this->countdown--;
    }
}