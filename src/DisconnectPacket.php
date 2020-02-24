<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\handler\PacketHandler;

class DisconnectPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = ProtocolInfo::DISCONNECT_PACKET;

	/** @var bool */
	public $hideDisconnectionScreen = false;
	/** @var string */
	public $message = "";

	public static function silent() : self{
		$result = new self;
		$result->hideDisconnectionScreen = true;
		return $result;
	}

	public static function message(string $message) : self{
		$result = new self;
		$result->hideDisconnectionScreen = false;
		$result->message = $message;
		return $result;
	}

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	protected function decodePayload() : void{
		$this->hideDisconnectionScreen = $this->buf->getBool();
		if(!$this->hideDisconnectionScreen){
			$this->message = $this->buf->getString();
		}
	}

	protected function encodePayload() : void{
		$this->buf->putBool($this->hideDisconnectionScreen);
		if(!$this->hideDisconnectionScreen){
			$this->buf->putString($this->message);
		}
	}

	public function handle(PacketHandler $handler) : bool{
		return $handler->handleDisconnect($this);
	}
}
