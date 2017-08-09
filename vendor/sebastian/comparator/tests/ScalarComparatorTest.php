<?php
/*
 * This file is part of the Comparator package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Comparator;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\ScalarComparator
 *
 */
class ScalarComparatorTest extends \PHPUnit_Framework_TestCase
{
	private $comparator;

	protected function setUp()
	{
		$this->comparator = new ScalarComparator;
	}

	public function acceptsSucceedsProvider()
	{
		return array(
			array("string", "string"),
			array(new ClassWithToString, "string"),
			array("string", new ClassWithToString),
			array("string", NULL),
			array(FALSE, "string"),
			array(FALSE, TRUE),
			array(NULL, FALSE),
			array(NULL, NULL),
			array("10", 10),
			array("", FALSE),
			array("1", TRUE),
			array(1, TRUE),
			array(0, FALSE),
			array(0.1, "0.1")
		);
	}

	public function acceptsFailsProvider()
	{
		return array(
			array(array(), array()),
			array("string", array()),
			array(new ClassWithToString, new ClassWithToString),
			array(FALSE, new ClassWithToString),
			array(tmpfile(), tmpfile())
		);
	}

	public function assertEqualsSucceedsProvider()
	{
		return array(
			array("string", "string"),
			array(new ClassWithToString, new ClassWithToString),
			array("string representation", new ClassWithToString),
			array(new ClassWithToString, "string representation"),
			array("string", "STRING", TRUE),
			array("STRING", "string", TRUE),
			array("String Representation", new ClassWithToString, TRUE),
			array(new ClassWithToString, "String Representation", TRUE),
			array("10", 10),
			array("", FALSE),
			array("1", TRUE),
			array(1, TRUE),
			array(0, FALSE),
			array(0.1, "0.1"),
			array(FALSE, NULL),
			array(FALSE, FALSE),
			array(TRUE, TRUE),
			array(NULL, NULL)
		);
	}

	public function assertEqualsFailsProvider()
	{
		$stringException = 'Failed asserting that two strings are equal.';
		$otherException = 'matches expected';

		return array(
			array("string", "other string", $stringException),
			array("string", "STRING", $stringException),
			array("STRING", "string", $stringException),
			array("string", "other string", $stringException),
			// https://github.com/sebastianbergmann/phpunit/issues/1023
			array('9E6666666', '9E7777777', $stringException),
			array(new ClassWithToString, "does not match", $otherException),
			array("does not match", new ClassWithToString, $otherException),
			array(0, 'Foobar', $otherException),
			array('Foobar', 0, $otherException),
			array("10", 25, $otherException),
			array("1", FALSE, $otherException),
			array("", TRUE, $otherException),
			array(FALSE, TRUE, $otherException),
			array(TRUE, FALSE, $otherException),
			array(NULL, TRUE, $otherException),
			array(0, TRUE, $otherException)
		);
	}

	/**
	 * @covers       ::accepts
	 * @dataProvider acceptsSucceedsProvider
	 */
	public function testAcceptsSucceeds($expected, $actual)
	{
		$this->assertTrue(
			$this->comparator->accepts($expected, $actual)
		);
	}

	/**
	 * @covers       ::accepts
	 * @dataProvider acceptsFailsProvider
	 */
	public function testAcceptsFails($expected, $actual)
	{
		$this->assertFalse(
			$this->comparator->accepts($expected, $actual)
		);
	}

	/**
	 * @covers       ::assertEquals
	 * @dataProvider assertEqualsSucceedsProvider
	 */
	public function testAssertEqualsSucceeds($expected, $actual, $ignoreCase = FALSE)
	{
		$exception = NULL;

		try
		{
			$this->comparator->assertEquals($expected, $actual, 0.0, FALSE, $ignoreCase);
		} catch (ComparisonFailure $exception)
		{
		}

		$this->assertNull($exception, 'Unexpected ComparisonFailure');
	}

	/**
	 * @covers       ::assertEquals
	 * @dataProvider assertEqualsFailsProvider
	 */
	public function testAssertEqualsFails($expected, $actual, $message)
	{
		$this->setExpectedException(
			'SebastianBergmann\\Comparator\\ComparisonFailure', $message
		);
		$this->comparator->assertEquals($expected, $actual);
	}
}
