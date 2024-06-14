<?php

declare(strict_types=1);

namespace Luthfi\AutoRestart;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

    private int $restartInterval;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        
        $seconds = $this->getConfig()->getNested("restart_interval.seconds", 0);
        $minutes = $this->getConfig()->getNested("restart_interval.minutes", 0);
        $hours = $this->getConfig()->getNested("restart_interval.hours", 1);

        $this->restartInterval = ($seconds + $minutes * 60 + $hours * 3600);

        if ($this->restartInterval > 0) {
            $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
                $this->scheduleRestart();
            }), 20);

            $this->getLogger()->info(TextFormat::GREEN . "AutoRestart enabled with a restart interval of {$this->restartInterval} seconds.");
        } else {
            $this->getLogger()->info(TextFormat::GREEN . "AutoRestart enabled with no scheduled restart interval.");
        }
    }

    private function scheduleRestart(): void {
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->getLogger()->info(TextFormat::YELLOW . "Scheduled server restart.");
            $this->getServer()->shutdown();
        }), $this->restartInterval * 20);
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "AutoRestart disabled.");
    }
}
