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
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;

class ExportTask extends AsyncTask{
  /** @var string */
  private $sender;
  /** @var string */
  private $fqdn;
  /** @var array */
  private $report;
  /** @var bool|string */
  private $reply = false;
  
  /**
   * @param string $sender
   * @param array $report
   */
  public function __construct(string $sender, string $fqdn, array $report){
    $this->sender = $sender;
    $this->fqdn = $fqdn;
    $this->report = $report;
  }
  
  public function onRun(){
    $this->reply = Utils::postURL($this->fqdn, [
      "player" => $this->report["player"],
      "date" => $this->report["date"],
      "messages" => json_encode((array) $this->report["messages"])]);
  }
  
  
  /**
   * @param Server $server
   */
  public function onCompletion(Server $server){
    if(($sender = $server->getPlayer($this->sender)) !== null or $sender === "CONSOLE"){
      if($this->reply !== false and filter_var($this->reply, FILTER_VALIDATE_URL)){
        $sender->sendMessage("Report for " . TextFormat::GREEN . $this->report["player"] . TextFormat::WHITE . " successfully uploaded.");
        $sender->sendMessage("URL: " . TextFormat::GREEN . $this->reply);
      }else{
        $sender->sendMessage(TextFormat::RED . "Error: host " . str_replace(["http://", "https://"], "", $this->fqdn) . " timed out. Please try again.");
      }
    }
  }
  
}
