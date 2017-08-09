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

use stdClass;

/**
 * @coversDefaultClass SebastianBergmann\Comparator\TypeComparator
 *
 */
class TypeComparatorTest extends \PHPUnit_Framework_TestCase
{
	private $comparator;

	protected function setUp()
	{
		$this->comparator = new TypeComparator;
	}

	public function acceptsSucceedsProvider()
	{
		return array(
			array(TRUE, 1),
			array(FALSE, array(1)),
			array(NULL, new stdClass),
			array(1.0, 5),
			array("", "")
		);
	}

	public function assertEqualsSucceedsProvider()
	{
		return array(
			array(TRUE, TRUE),
			array(TRUE, FALSE),
			array(FALSE, FALSE),
			array(NULL, NULL),
			array(new stdClass, new stdClass),
			array(0, 0),
			array(1.0, 2.0),
			array("hello", "world"),
			array("", ""),
			array(array(), array(1, 2, 3))
		);
	}

	public function assertEqualsFailsProvider()
	{
		return array(
			array(TRUE, NULL),
			array(NULL, FALSE),
			array(1.0, 0),
			array(new stdClass, array()),
			array("1", 1)
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
	 * @covers       ::assertEquals
	 * @dataProvider assertEqualsSucceedsProvider
	 */
	public function testAssertEqualsSucceeds($expected, $actual)
	{
		$exception = NULL;

		try
		{
			$this->comparator->assertEquals($expected, $actual);
		} catch (ComparisonFailure $exception)
		{
		}

		$this->assertNull($exception, 'Unexpected ComparisonFailure');
	}

	/**
	 * @covers       ::assertEquals
	 * @dataProvider assertEqualsFailsProvider
	 */
	public function testAssertEqualsFails($expected, $actual)
	{
		$this->setExpectedException('SebastianBergmann\\Comparator\\ComparisonFailure', 'does not match expected type');
		$this->comparator->assertEquals($expected, $actual);
	}
}
