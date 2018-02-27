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
  
namespace ChatLogger\event;

use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class PlayerChatLogEvent extends PluginEvent implements Cancellable{
  public static $handlerList = null;
  
  /** @var Player */
  private $player;
  /** @var int */
  private $time;
  /** @var string */
  private $message;
  
  /**
   * @param Player $player
   * @param int $time
   * @param string $message
   */
  public function __construct(Player $player, int $time, string $message){
    $this->player = $player;
    $this->time = $time;
    $this->message = $message;
  }
  
  /**
   * Returns the player who was chat logged.
   *
   * @return Player
   */
  public function getPlayer() : Player{
    return $this->player;
  }
  
  /**
   * Returns the timestamp the player was chat logged at.
   *
   * @return int
   */
  public function getTime() : int{
    return $this->time;
  }
  
  /**
   * Returns the player chat message.
   *
   * @return string
   */
  public function getMessage() : string{
    return $this->message;
  }
  
}
