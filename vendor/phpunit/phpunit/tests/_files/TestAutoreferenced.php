<?php
use PHPUnit\Framework\TestCase;

class TestAutoreferenced extends TestCase
{
	public $myTestData = NULL;

	public function testJsonEncodeException($data)
	{
		$this->myTestData = $data;
	}
}
