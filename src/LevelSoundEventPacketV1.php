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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\handler\PacketHandler;

/**
 * Useless leftover from a 1.8 refactor, does nothing
 */
class LevelSoundEventPacketV1 extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_SOUND_EVENT_PACKET_V1;

	/** @var int */
	public $sound;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $extraData = 0;
	/** @var int */
	public $entityType = 1;
	/** @var bool */
	public $isBabyMob = false; //...
	/** @var bool */
	public $disableRelativeVolume = false;

	protected function decodePayload() : void{
		$this->sound = $this->buf->getByte();
		$this->position = $this->buf->getVector3();
		$this->extraData = $this->buf->getVarInt();
		$this->entityType = $this->buf->getVarInt();
		$this->isBabyMob = $this->buf->getBool();
		$this->disableRelativeVolume = $this->buf->getBool();
	}

	protected function encodePayload() : void{
		$this->buf->putByte($this->sound);
		$this->buf->putVector3($this->position);
		$this->buf->putVarInt($this->extraData);
		$this->buf->putVarInt($this->entityType);
		$this->buf->putBool($this->isBabyMob);
		$this->buf->putBool($this->disableRelativeVolume);
	}

	public function handle(PacketHandler $handler) : bool{
		return $handler->handleLevelSoundEventPacketV1($this);
	}
}
