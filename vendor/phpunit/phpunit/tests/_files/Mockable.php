<?php

class Mockable
{
	public $constructorCalled = FALSE;
	public $cloned = FALSE;

	public function __construct()
	{
		$this->constructorCalled = FALSE;
	}

	public function foo()
	{
		return TRUE;
	}

	public function bar()
	{
		return TRUE;
	}

	public function __clone()
	{
		$this->cloned = TRUE;
	}
}
