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

namespace ChatLogger\provider;

use pocketmine\Player;
use pocketmine\utils\Config;

use ChatLogger\ChatLogger;

class YamlProvider implements Provider{
  
  /** @var ChatLogger */
  private $plugin;
  /** @var array|null */
  private $chatlog = null;
  
  public function __construct(ChatLogger $plugin){
    $this->plugin = $plugin;
  }
  
  public function open() : bool{
    if($this->chatlog !== null){
      return false;
    }
    $this->chatlog = (new Config($this->plugin->getDataFolder() . "chatlog.yml", Config::YAML))->getAll();
    return true;
  }
  
  public function getName() : string{
    return "Yaml";
  }
  
  public function chattedBefore(string $player) : bool{
    return isset($this->chatlog[strtolower($player)]);
  }
  
  public function logMessage(Player $player, int $time, string $message) : void{
    $this->chatlog[strtolower($player->getName())][] = [$time, $message];
  }
  
  public function getAll() : array{
    return $this->chatlog;
  }
  
  public function close() : void{
    $chatlog = new Config($this->plugin->getDataFolder() . "chatlog.yml", Config::YAML);
    $chatlog->setAll($this->chatlog);
    $chatlog->save();
  }
