## Registering a custom InvMenu type
So let's say you'd like to send players a dispenser inventory. While InvMenu doesn't ship with a `InvMenu::TYPE_DISPENSER`, you can still create a dispenser InvMenu by registering an `InvMenuType` object with the information about what a dispenser inventory looks like.
```php
public const TYPE_DISPENSER = "myplugin:dispenser";

protected function onEnable() : void{
	InvMenuHandler::getTypeRegistry()->register(self::TYPE_DISPENSER, InvMenuTypeBuilders::BLOCK_ACTOR_FIXED()
		->setBlock(BlockFactory::getInstance()->get(BlockLegacyIds::DISPENSER, 0))
		->setBlockActorId("Dispenser")
		->setSize(9)
		->setNetworkWindowType(WindowTypes::DISPENSER)
	->build());
}
```
Sweet! Now you can create a dispenser menu using
```php
$menu = InvMenu::create(self::TYPE_DISPENSER);
```

## InvMenu Wiki
Applications, examples, tutorials and featured projects using InvMenu can be found on the [InvMenu Wiki](https://github.com/Muqsit/InvMenu/wiki/InvMenu-v4.0).

