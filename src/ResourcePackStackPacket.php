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
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackStackEntry;
use function count;

class ResourcePackStackPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;

	/** @var bool */
	public $mustAccept = false;

	/** @var ResourcePackStackEntry[] */
	public $behaviorPackStack = [];
	/** @var ResourcePackStackEntry[] */
	public $resourcePackStack = [];

	/** @var bool */
	public $isExperimental = false;
	/** @var string */
	public $baseGameVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;

	/**
	 * @param ResourcePackStackEntry[] $resourcePacks
	 * @param ResourcePackStackEntry[] $behaviorPacks
	 *
	 * @return ResourcePackStackPacket
	 */
	public static function create(array $resourcePacks, array $behaviorPacks, bool $mustAccept, bool $isExperimental = false) : self{
		$result = new self;
		$result->mustAccept = $mustAccept;
		$result->resourcePackStack = $resourcePacks;
		$result->behaviorPackStack = $behaviorPacks;
		$result->isExperimental = $isExperimental;
		return $result;
	}

	protected function decodePayload() : void{
		$this->mustAccept = $this->buf->getBool();
		$behaviorPackCount = $this->buf->getUnsignedVarInt();
		while($behaviorPackCount-- > 0){
			$this->behaviorPackStack[] = ResourcePackStackEntry::read($this->buf);
		}

		$resourcePackCount = $this->buf->getUnsignedVarInt();
		while($resourcePackCount-- > 0){
			$this->resourcePackStack[] = ResourcePackStackEntry::read($this->buf);
		}

		$this->isExperimental = $this->buf->getBool();
		$this->baseGameVersion = $this->buf->getString();
	}

	protected function encodePayload() : void{
		$this->buf->putBool($this->mustAccept);

		$this->buf->putUnsignedVarInt(count($this->behaviorPackStack));
		foreach($this->behaviorPackStack as $entry){
			$entry->write($this->buf);
		}

		$this->buf->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$entry->write($this->buf);
		}

		$this->buf->putBool($this->isExperimental);
		$this->buf->putString($this->baseGameVersion);
	}

	public function handle(PacketHandler $handler) : bool{
		return $handler->handleResourcePackStack($this);
	}
}
