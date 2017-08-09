<?php

class Singleton
{
	private static $uniqueInstance = NULL;

	protected function __construct()
	{
	}

	final private function __clone()
	{
	}

	public static function getInstance()
	{
		if (self::$uniqueInstance === NULL)
		{
			self::$uniqueInstance = new self;
		}

		return self::$uniqueInstance;
	}
}
