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

use ChatLogger\ChatLogger;

interface Provider{
  
  /**
   * @param ChatLogger $plugin
   */
  public function __construct(ChatLogger $plugin);
  
  /**
   * Opens the database provider.
   *
   * @return bool
   */
  public function open() : bool;
  
  /**
   * Returns the database provider name.
   *
   * @return string
   */
  public function getName() : string;
  
  /**
   * Returns whether the player has chat history or not
   *
   * @param string $player
   *
   * @return bool
   */
  public function chattedBefore(string $player) : bool;
  
  /**
   * Adds the player chat message to the chat log.
   *
   * @param Player $player
   * @param int $time
   * @param string $message
   */
  public function logMessage(Player $player, int $time, string $message) : void;
  
  /**
   * Returns all the messages sent by a player.
   *
   * @param string $player
   *
   * @return array
   */
  public function getMessages(string $player) : array;
  
  /**
   * Closes the database provider.
   */
  public function close() : void;
  
}
