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

namespace ChatLogger\task;

use pocketmine\command\CommandSender;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

use ChatLogger\ChatLogger;

class ExportTask extends AsyncTask{
  
  /** @var ChatLogger */
  private $plugin;
  /** @var CommandSender */
  private $sender;
  /** @var array */
  private $report;
  /** @var string|bool */
  private $reply = false;
  
  /**
   * @param ChatLogger $plugin
   * @param CommandSender $sender
   * @param array $report
   */
  public function __construct(ChatLogger $plugin, CommandSender $sender, array $report){
    $this->plugin = $plugin;
    $this->sender = $sender;
    $this->report = $report;
  }
  
  public function onRun(){
    $url = ($this->plugin->getConfig()->getNested("report.use-https", true) ? "https" : "http") . "://" . $this->plugin->getConfig()->getNested("report.host", "chatlogger.herokuapp.com") . "/api/";
    $this->reply = Utils::postURL($url, $this->report);
  }
  
  /**
   * @param Server $server
   */
  public function onCompletion(Server $server){
    if($this->reply !== false and filter_var($this->reply, FILTER_VALIDATE_URL)){
      $this->sender->sendMessage("Report for " . TextFormat::GREEN . $this->report["player"] . TextFormat::WHITE . " successfully uploaded.");
      $this->sender->sendMessage("URL: " . TextFormat::GREEN . $this->reply);
    }else{
      $this->sender->sendMessage(TextFormat::RED . "Error: host " . $this->plugin->getConfig()->getNested("report.host", "chatlogger.herokuapp.com") . " timed out.");
    }
  }
  
}
