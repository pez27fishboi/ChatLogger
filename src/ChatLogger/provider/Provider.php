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
  public function __construct(ChatLogger $plugin){
  }
  
  /**
   * @return bool
   */
  public function open() : bool;
  
  /**
   * @return string
   */
  public function getName() : string;
  
  /**
   * @param string $player
   *
   * @return bool
   */
  public function chattedBefore(string $player) : bool;
  
  /**
   * @param Player $player
   *
   * @return void
   */
  public function logMessage(Player $player) : void;
  
  /**
   * @return array
   */
  public function getAll() : array;
  
  /**
   * @return void
   */
  public function close() : void;
