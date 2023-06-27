<?php

declare(strict_types=1);

namespace muqsit\invmenu\type\graphic;

use muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use pocketmine\block\Block;
use pocketmine\block\tile\Spawnable;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;

final class BlockInvMenuGraphic implements PositionedInvMenuGraphic{

	public function __construct(
		readonly private Block $block,
		readonly private Vector3 $position,
		readonly private ?InvMenuGraphicNetworkTranslator $network_translator = null,
		readonly private int $animation_duration = 0
	){}

	public function getPosition() : Vector3{
		return $this->position;
	}

	public function send(Player $player, ?string $name) : void{
		$network = $player->getNetworkSession();

		$network->sendDataPacket(UpdateBlockPacket::create(
			BlockPosition::fromVector3($this->position),
            TypeConverter::getInstance($network->getProtocolId())->getBlockTranslator()->internalIdToNetworkId($this->block->getStateId()),
			UpdateBlockPacket::FLAG_NETWORK,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		));
	}

	public function sendInventory(Player $player, Inventory $inventory) : bool{
		return $player->setCurrentWindow($inventory);
	}

	public function remove(Player $player) : void{
		$network = $player->getNetworkSession();
		$world = $player->getWorld();
		$blockPosition = BlockPosition::fromVector3($this->position);
		$block = $world->getBlockAt($this->position->x, $this->position->y, $this->position->z);
		$network->sendDataPacket(UpdateBlockPacket::create(
			$blockPosition,
            $network->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId($block->getStateId()),
			UpdateBlockPacket::FLAG_NETWORK,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		), true);

		$tile = $world->getTileAt($this->position->x, $this->position->y, $this->position->z);
		if($tile instanceof Spawnable){
			$network->sendDataPacket(BlockActorDataPacket::create($blockPosition, $tile->getSerializedSpawnCompound($network->getTypeConverter())), true);
		}
	}

	public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator{
		return $this->network_translator;
	}

	public function getAnimationDuration() : int{
		return $this->animation_duration;
	}
}
