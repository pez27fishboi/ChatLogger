<?php

/*
 * ChatLogger - A PocketMine-MP plugin to log your server chat
 * Copyright (C) 2017 Kevin Andrews <https://github.com/kenygamer/ChatLogger>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

declare(strict_types=1);

namespace ChatLogger;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use ChatLogger\event\PlayerChatLogEvent;
use ChatLogger\task\ExportTask;

class ChatLogger extends PluginBase implements Listener{
  
  /** @var array */
  private $chatlog;
  
  public function onEnable() : void{
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!is_dir($this->getDataFolder())){
      @mkdir($this->getDataFolder());
    }
    $this->saveDefaultConfig();
    $this->chatlog = (new Config($this->getDataFolder()."chatlog.yml", Config::YAML))->getAll();
  }
  
  public function onDisable() : void{
    $chatlog = new Config($this->getDataFolder()."chatlog.yml", Config::YAML);
    $chatlog->setAll($this->chatlog);
    $chatlog->save();
  }
  
  /**
   * @param CommandSender $sender
   * @param Command $cmd
   * @param string $label
   * @param array $args
   *
   * @return bool
   */
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
    if(!$sender instanceof Player){
      $sender->sendMessage(TextFormat::RED . "Command must be used in-game.");
      return true;
    }
    if(count($args) !== 2) return false;
    
    $player = strtolower($args[0]);
    $date = $args[1];
    
    if(!isset($this->chatlog[$player])){
      $sender->sendMessage(TextFormat::RED . "Error: Player {$player} has no chat history.");
      return true;
    }
    
    if(!preg_match_all("/^((0|1)\d{1})-((0|1|2)\d{1})-((19|20)\d{2})/", $date)){
      $sender->sendMessage(TextFormat::RED . "Error: Please write date using right format.");
      return true;
    }
    
    $sender->sendMessage("Step 1 of 2: Generating report...");
    
    $report = [];
    foreach($this->chatlog[$player] as $message){
      if(date("m-d-Y", $message[0]) === $date) $report[] = $message;
    }
    
    if(empty($report)){
      $sender->sendMessage(TextFormat::RED . "Error: Player {$player} has no chat history for this date.");
      return true;
    }
    
    $sender->sendMessage("Step 2 of 2: Uploading report...");
    $sender->sendMessage("Report is being uploaded in the background");
    
    array_push($report, [
      "player" => $player,
      "date" => $date
      ]);
    $this->getServer()->getScheduler()->scheduleAsyncTask(new ExportTask($this, $sender, $report));
    return true;
  }
  
  /**
   * @param PlayerChatEvent $event
   */
  public function onChat(PlayerChatEvent $event) : void{
    if($event->isCancelled()){
      $this->getLogger()->debug("Failed to log chat message: PlayerChatEvent is cancelled");
      return;
    }
    
    $player = $event->getPlayer();
    if($player->hasPermission("chatlogger") || $player->hasPermission("chatlogger.bypass")){
      $this->getLogger()->debug("Failed to log chat message: " . $player->getName() . " has chatlogger|chatlogger.bypass permission");
      return;
    }
    
    $time = time();
    $message = $event->getMessage();
    
    $this->getServer()->getPluginManager()->callEvent($event = new PlayerChatLogEvent($player, $time, $message));
    if(!$event->isCancelled() or $this->getConfig()->get("force", false) === true){
      $this->chatlog[strtolower($player->getName())][] = [$time, $message];
      return;
    }
    $this->getLogger()->debug("Failed to log chat message: PlayerChatLogEvent is cancelled");
  }
  
}
