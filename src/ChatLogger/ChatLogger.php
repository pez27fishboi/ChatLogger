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
use pocketmine\utils\Utils;

use ChatLogger\event\PlayerChatLogEvent;

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
    $cfg = new Config($this->getDataFolder()."chatlog.yml", Config::YAML);
    $cfg->setAll($this->chatlog);
    $cfg->save();
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
      $sender->sendMessage(TextFormat::RED . "Player " . $player . " never chatted before");
      return true;
    }
    
    if(!preg_match_all("/^((0|1)\d{1})-((0|1|2)\d{1})-((19|20)\d{2})/", $date)){
      $sender->sendMessage(TextFormat::RED . "Please write date using right format");
      return true;
    }
    
    $sender->sendMessage("Generating report... This can take some time");
    
    $report = [];
    foreach($this->chatlog[$player] as $message){
      if(date("m-d-Y", $message[0]) === $date) $report[] = $message;
    }
    
    if(empty($report)){
      $sender->sendMessage(TextFormat::RED . "Player " . $player . " did not chat at this date");
      return true;
    }
    
    $url = ($this->getConfig()->getNested("report.use-https", true) ? "https" : "http") . "://" . $this->getConfig()->getNested("report.host", "chatlogger.herokuapp.com") . "/api.php";
    $reply = Utils::postURL($url, [
      "report" => "yes",
      "player" => $player,
      "date" => $date,
      "json" => json_encode($report)
      ]);
    
    if($reply !== false and ($data = json_decode($reply)) !== null and isset($data->reportUrl)){
      $reportUrl = $data->reportUrl;
      $sender->sendMessage("Report for " . TextFormat::GREEN . $args[0] . TextFormat::WHITE . " successfully generated. See " . TextFormat::GREEN . $reportUrl);
      return true;
    }
    
    $sender->sendMessage(TextFormat::RED . "Failed to create report: host " . $this->getConfig()->getNested("report.host", "chatlogger.herokuapp.com") . " is unavailable");
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
    if(!$event->isCancelled() or $this->getConfig()->get("chatlog-force", false) === true){
      $this->chatlog[strtolower($player->getName())][] = [
        $time,
        $message
        ];
      return;
    }
    $this->getLogger()->debug("Failed to log chat message: PlayerChatLogEvent is cancelled");
  }
  
}
