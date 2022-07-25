<?php

namespace parkour;

//USES:

use parkour\commands\ParkourCommand;
use parkour\Listener\ParkourListener;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use function floor;
use function print_r;
use function var_dump;

class ParkourMain extends PluginBase {
    use SingletonTrait;


    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new ParkourListener(), $this);
        $this->getServer()->getCommandMap()->register("parkour", new ParkourCommand());
    }

    public static function setSpawns(Player $player, string $arena): bool {
        $ps = $player->getLocation();
        $config = self::getArenaConfig();

        if($config->exists($arena)) {
            $player->sendMessage("§cThis Parkour already exists!");
            return false;
        }
        $config->set($arena, implode(
            "|",
            [
                $ps->getX(),
                $ps->getY(),
                $ps->getZ(),
                $ps->getWorld()->getFolderName(),
                $ps->getYaw(),
                $ps->getPitch(),
            ]
        ));
        $config->save();
        return true;
    }

    public static function getArenaConfig(): Config {
        return new Config(self::getInstance()->getDataFolder() . "arenas.yml", Config::YAML);
    }

    public static function getParkourConfig(): Config {
        return self::getInstance()->getConfig();
    }

    public static function stringToLocation(string $locationString): Location {
        $locArr = explode('|', $locationString);
        return Location::fromObject(new Vector3(
            $locArr[0], $locArr[1], $locArr[2]),
            Server::getInstance()->getWorldManager()->getWorldByName($locArr[3]),
            $locArr[4], $locArr[5]);
    }

    public static function getArena(string $arena): ?Location {
        $c = self::getArenaConfig();

        if($c->exists($arena))
            return self::stringToLocation($c->get($arena));
        return null;
    }

    public static function deleteSpawns(Player $player, string $arena): bool {
        $config = self::getArenaConfig();

        if ($config->exists($arena)) {
            $config->remove($arena);
            $config->save();
            return true;
        }
        return false;
    }

    public static function checkpointConfig() {
        return new Config(self::getInstance()->getDataFolder() . "checkpoints.yml", Config::YAML);
    }

    public static function setCheckpoint(Player $player, string $arena): bool {
        $c = self::checkpointConfig();
        $ps = $player->getLocation();

        if($c->exists($arena))
            return false;
        if(is_array($c->get($arena))) {
            $content = $c->get($arena, []);

            $content[] = implode(
                "|",
                [
                    floor($ps->getX()),
                    floor($ps->getY() -1),
                    floor($ps->getZ()),
                    $ps->getWorld()->getFolderName(),
                ]
            );
            $c->set($arena, $content);
        } else {
            $c->set($arena, [implode(
                "|",
                [
                    floor($ps->getX()),
                    floor($ps->getY() -1),
                    floor($ps->getZ()),
                    $ps->getWorld()->getFolderName(),
                ])]);
        }
        $c->save();
        return true;
    }

    public static function getCheckpoint(Position $pos, string $arena): bool {
        if(!self::checkpointConfig()->exists($arena))
            return false;
        var_dump(self::checkpointConfig()->get($arena, []));
        $cp = self::checkpointConfig()->get($arena, []);
        return in_array(implode(
            "|",
            [
                floor($pos->getX()),
                floor($pos->getY() -1),
                floor($pos->getZ()),
                $pos->getWorld()->getFolderName()

            ]), $cp);
    }
}
