<?php

class MultipleDataProviderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerA
	 * @dataProvider providerB
	 * @dataProvider providerC
	 */
	public function testOne()
	{
	}

	/**
	 * @dataProvider providerD
	 * @dataProvider providerE
	 * @dataProvider providerF
	 */
	public function testTwo()
	{
	}

	public static function providerA()
	{
		return [
			['ok', NULL, NULL],
			['ok', NULL, NULL],
			['ok', NULL, NULL]
		];
	}

	public static function providerB()
	{
		return [
			[NULL, 'ok', NULL],
			[NULL, 'ok', NULL],
			[NULL, 'ok', NULL]
		];
	}

	public static function providerC()
	{
		return [
			[NULL, NULL, 'ok'],
			[NULL, NULL, 'ok'],
			[NULL, NULL, 'ok']
		];
	}

	public static function providerD()
	{
		yield ['ok', NULL, NULL];
		yield ['ok', NULL, NULL];
		yield ['ok', NULL, NULL];
	}

	public static function providerE()
	{
		yield [NULL, 'ok', NULL];
		yield [NULL, 'ok', NULL];
		yield [NULL, 'ok', NULL];
	}

	public static function providerF()
	{
		$object = new ArrayObject(
			[
				[NULL, NULL, 'ok'],
				[NULL, NULL, 'ok'],
				[NULL, NULL, 'ok']
			]
		);

		return $object->getIterator();
	}
}
