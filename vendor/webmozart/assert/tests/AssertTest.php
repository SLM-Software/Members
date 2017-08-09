<?php

/*
 * This file is part of the webmozart/assert package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Assert\Tests;

use ArrayIterator;
use Exception;
use Error;
use LogicException;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use stdClass;
use Webmozart\Assert\Assert;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AssertTest extends PHPUnit_Framework_TestCase
{
	private static $resource;

	public static function getResource()
	{
		if (!static::$resource)
		{
			static::$resource = fopen(__FILE__, 'r');
		}

		return static::$resource;
	}

	public static function tearDownAfterClass()
	{
		@fclose(self::$resource);
	}

	public function getTests()
	{
		$resource = self::getResource();

		return array(
			array('string', array('value'), TRUE),
			array('string', array(''), TRUE),
			array('string', array(1234), FALSE),
			array('stringNotEmpty', array('value'), TRUE),
			array('stringNotEmpty', array(''), FALSE),
			array('stringNotEmpty', array(1234), FALSE),
			array('integer', array(123), TRUE),
			array('integer', array('123'), FALSE),
			array('integer', array(1.0), FALSE),
			array('integer', array(1.23), FALSE),
			array('integerish', array(1.0), TRUE),
			array('integerish', array(1.23), FALSE),
			array('integerish', array(123), TRUE),
			array('integerish', array('123'), TRUE),
			array('float', array(1.0), TRUE),
			array('float', array(1.23), TRUE),
			array('float', array(123), FALSE),
			array('float', array('123'), FALSE),
			array('numeric', array(1.0), TRUE),
			array('numeric', array(1.23), TRUE),
			array('numeric', array(123), TRUE),
			array('numeric', array('123'), TRUE),
			array('numeric', array('foo'), FALSE),
			array('boolean', array(TRUE), TRUE),
			array('boolean', array(FALSE), TRUE),
			array('boolean', array(1), FALSE),
			array('boolean', array('1'), FALSE),
			array('scalar', array('1'), TRUE),
			array('scalar', array(123), TRUE),
			array('scalar', array(TRUE), TRUE),
			array('scalar', array(NULL), FALSE),
			array('scalar', array(array()), FALSE),
			array('scalar', array(new stdClass()), FALSE),
			array('object', array(new stdClass()), TRUE),
			array('object', array(new RuntimeException()), TRUE),
			array('object', array(NULL), FALSE),
			array('object', array(TRUE), FALSE),
			array('object', array(1), FALSE),
			array('object', array(array()), FALSE),
			array('resource', array($resource), TRUE),
			array('resource', array($resource, 'stream'), TRUE),
			array('resource', array($resource, 'other'), FALSE),
			array('resource', array(1), FALSE),
			array('isCallable', array('strlen'), TRUE),
			array('isCallable', array(array($this, 'getTests')), TRUE),
			array('isCallable', array(function ()
			                          {
			                          }), TRUE),
			array('isCallable', array(1234), FALSE),
			array('isCallable', array('foobar'), FALSE),
			array('isArray', array(array()), TRUE),
			array('isArray', array(array(1, 2, 3)), TRUE),
			array('isArray', array(new ArrayIterator(array())), FALSE),
			array('isArray', array(123), FALSE),
			array('isArray', array(new stdClass()), FALSE),
			array('isTraversable', array(array()), TRUE),
			array('isTraversable', array(array(1, 2, 3)), TRUE),
			array('isTraversable', array(new ArrayIterator(array())), TRUE),
			array('isTraversable', array(123), FALSE),
			array('isTraversable', array(new stdClass()), FALSE),
			array('isInstanceOf', array(new stdClass(), 'stdClass'), TRUE),
			array('isInstanceOf', array(new Exception(), 'stdClass'), FALSE),
			array('isInstanceOf', array(123, 'stdClass'), FALSE),
			array('isInstanceOf', array(array(), 'stdClass'), FALSE),
			array('notInstanceOf', array(new stdClass(), 'stdClass'), FALSE),
			array('notInstanceOf', array(new Exception(), 'stdClass'), TRUE),
			array('notInstanceOf', array(123, 'stdClass'), TRUE),
			array('notInstanceOf', array(array(), 'stdClass'), TRUE),
			array('true', array(TRUE), TRUE),
			array('true', array(FALSE), FALSE),
			array('true', array(1), FALSE),
			array('true', array(NULL), FALSE),
			array('false', array(FALSE), TRUE),
			array('false', array(TRUE), FALSE),
			array('false', array(1), FALSE),
			array('false', array(0), FALSE),
			array('false', array(NULL), FALSE),
			array('null', array(NULL), TRUE),
			array('null', array(FALSE), FALSE),
			array('null', array(0), FALSE),
			array('notNull', array(FALSE), TRUE),
			array('notNull', array(0), TRUE),
			array('notNull', array(NULL), FALSE),
			array('isEmpty', array(NULL), TRUE),
			array('isEmpty', array(FALSE), TRUE),
			array('isEmpty', array(0), TRUE),
			array('isEmpty', array(''), TRUE),
			array('isEmpty', array(1), FALSE),
			array('isEmpty', array('a'), FALSE),
			array('notEmpty', array(1), TRUE),
			array('notEmpty', array('a'), TRUE),
			array('notEmpty', array(NULL), FALSE),
			array('notEmpty', array(FALSE), FALSE),
			array('notEmpty', array(0), FALSE),
			array('notEmpty', array(''), FALSE),
			array('eq', array(1, 1), TRUE),
			array('eq', array(1, '1'), TRUE),
			array('eq', array(1, TRUE), TRUE),
			array('eq', array(1, 0), FALSE),
			array('notEq', array(1, 0), TRUE),
			array('notEq', array(1, 1), FALSE),
			array('notEq', array(1, '1'), FALSE),
			array('notEq', array(1, TRUE), FALSE),
			array('same', array(1, 1), TRUE),
			array('same', array(1, '1'), FALSE),
			array('same', array(1, TRUE), FALSE),
			array('same', array(1, 0), FALSE),
			array('notSame', array(1, 0), TRUE),
			array('notSame', array(1, 1), FALSE),
			array('notSame', array(1, '1'), TRUE),
			array('notSame', array(1, TRUE), TRUE),
			array('greaterThan', array(1, 0), TRUE),
			array('greaterThan', array(0, 0), FALSE),
			array('greaterThanEq', array(2, 1), TRUE),
			array('greaterThanEq', array(1, 1), TRUE),
			array('greaterThanEq', array(0, 1), FALSE),
			array('lessThan', array(0, 1), TRUE),
			array('lessThan', array(1, 1), FALSE),
			array('lessThanEq', array(0, 1), TRUE),
			array('lessThanEq', array(1, 1), TRUE),
			array('lessThanEq', array(2, 1), FALSE),
			array('range', array(1, 1, 2), TRUE),
			array('range', array(2, 1, 2), TRUE),
			array('range', array(0, 1, 2), FALSE),
			array('range', array(3, 1, 2), FALSE),
			array('oneOf', array(1, array(1, 2, 3)), TRUE),
			array('oneOf', array(1, array('1', '2', '3')), FALSE),
			array('contains', array('abcd', 'ab'), TRUE),
			array('contains', array('abcd', 'bc'), TRUE),
			array('contains', array('abcd', 'cd'), TRUE),
			array('contains', array('abcd', 'de'), FALSE),
			array('contains', array('', 'de'), FALSE),
			array('startsWith', array('abcd', 'ab'), TRUE),
			array('startsWith', array('abcd', 'bc'), FALSE),
			array('startsWith', array('', 'bc'), FALSE),
			array('startsWithLetter', array('abcd'), TRUE),
			array('startsWithLetter', array('1abcd'), FALSE),
			array('startsWithLetter', array(''), FALSE),
			array('endsWith', array('abcd', 'cd'), TRUE),
			array('endsWith', array('abcd', 'bc'), FALSE),
			array('endsWith', array('', 'bc'), FALSE),
			array('regex', array('abcd', '~^ab~'), TRUE),
			array('regex', array('abcd', '~^bc~'), FALSE),
			array('regex', array('', '~^bc~'), FALSE),
			array('alpha', array('abcd'), TRUE),
			array('alpha', array('ab1cd'), FALSE),
			array('alpha', array(''), FALSE),
			array('digits', array('1234'), TRUE),
			array('digits', array('12a34'), FALSE),
			array('digits', array(''), FALSE),
			array('alnum', array('ab12'), TRUE),
			array('alnum', array('ab12$'), FALSE),
			array('alnum', array(''), FALSE),
			array('lower', array('abcd'), TRUE),
			array('lower', array('abCd'), FALSE),
			array('lower', array('ab_d'), FALSE),
			array('lower', array(''), FALSE),
			array('upper', array('ABCD'), TRUE),
			array('upper', array('ABcD'), FALSE),
			array('upper', array('AB_D'), FALSE),
			array('upper', array(''), FALSE),
			array('length', array('abcd', 4), TRUE),
			array('length', array('abc', 4), FALSE),
			array('length', array('abcde', 4), FALSE),
			array('length', array('äbcd', 4), TRUE, TRUE),
			array('length', array('äbc', 4), FALSE, TRUE),
			array('length', array('äbcde', 4), FALSE, TRUE),
			array('minLength', array('abcd', 4), TRUE),
			array('minLength', array('abcde', 4), TRUE),
			array('minLength', array('abc', 4), FALSE),
			array('minLength', array('äbcd', 4), TRUE, TRUE),
			array('minLength', array('äbcde', 4), TRUE, TRUE),
			array('minLength', array('äbc', 4), FALSE, TRUE),
			array('maxLength', array('abcd', 4), TRUE),
			array('maxLength', array('abc', 4), TRUE),
			array('maxLength', array('abcde', 4), FALSE),
			array('maxLength', array('äbcd', 4), TRUE, TRUE),
			array('maxLength', array('äbc', 4), TRUE, TRUE),
			array('maxLength', array('äbcde', 4), FALSE, TRUE),
			array('lengthBetween', array('abcd', 3, 5), TRUE),
			array('lengthBetween', array('abc', 3, 5), TRUE),
			array('lengthBetween', array('abcde', 3, 5), TRUE),
			array('lengthBetween', array('ab', 3, 5), FALSE),
			array('lengthBetween', array('abcdef', 3, 5), FALSE),
			array('lengthBetween', array('äbcd', 3, 5), TRUE, TRUE),
			array('lengthBetween', array('äbc', 3, 5), TRUE, TRUE),
			array('lengthBetween', array('äbcde', 3, 5), TRUE, TRUE),
			array('lengthBetween', array('äb', 3, 5), FALSE, TRUE),
			array('lengthBetween', array('äbcdef', 3, 5), FALSE, TRUE),
			array('fileExists', array(__FILE__), TRUE),
			array('fileExists', array(__DIR__), TRUE),
			array('fileExists', array(__DIR__ . '/foobar'), FALSE),
			array('file', array(__FILE__), TRUE),
			array('file', array(__DIR__), FALSE),
			array('file', array(__DIR__ . '/foobar'), FALSE),
			array('directory', array(__DIR__), TRUE),
			array('directory', array(__FILE__), FALSE),
			array('directory', array(__DIR__ . '/foobar'), FALSE),
			// no tests for readable()/writable() for now
			array('classExists', array(__CLASS__), TRUE),
			array('classExists', array(__NAMESPACE__ . '\Foobar'), FALSE),
			array('subclassOf', array(__CLASS__, 'PHPUnit_Framework_TestCase'), TRUE),
			array('subclassOf', array(__CLASS__, 'stdClass'), FALSE),
			array('implementsInterface', array('ArrayIterator', 'Traversable'), TRUE),
			array('implementsInterface', array(__CLASS__, 'Traversable'), FALSE),
			array('propertyExists', array((object)array('property' => 0), 'property'), TRUE),
			array('propertyExists', array((object)array('property' => NULL), 'property'), TRUE),
			array('propertyExists', array((object)array('property' => NULL), 'foo'), FALSE),
			array('propertyNotExists', array((object)array('property' => 0), 'property'), FALSE),
			array('propertyNotExists', array((object)array('property' => NULL), 'property'), FALSE),
			array('propertyNotExists', array((object)array('property' => NULL), 'foo'), TRUE),
			array('methodExists', array('RuntimeException', 'getMessage'), TRUE),
			array('methodExists', array(new RuntimeException(), 'getMessage'), TRUE),
			array('methodExists', array('stdClass', 'getMessage'), FALSE),
			array('methodExists', array(new stdClass(), 'getMessage'), FALSE),
			array('methodExists', array(NULL, 'getMessage'), FALSE),
			array('methodExists', array(TRUE, 'getMessage'), FALSE),
			array('methodExists', array(1, 'getMessage'), FALSE),
			array('methodNotExists', array('RuntimeException', 'getMessage'), FALSE),
			array('methodNotExists', array(new RuntimeException(), 'getMessage'), FALSE),
			array('methodNotExists', array('stdClass', 'getMessage'), TRUE),
			array('methodNotExists', array(new stdClass(), 'getMessage'), TRUE),
			array('methodNotExists', array(NULL, 'getMessage'), TRUE),
			array('methodNotExists', array(TRUE, 'getMessage'), TRUE),
			array('methodNotExists', array(1, 'getMessage'), TRUE),
			array('keyExists', array(array('key' => 0), 'key'), TRUE),
			array('keyExists', array(array('key' => NULL), 'key'), TRUE),
			array('keyExists', array(array('key' => NULL), 'foo'), FALSE),
			array('keyNotExists', array(array('key' => 0), 'key'), FALSE),
			array('keyNotExists', array(array('key' => NULL), 'key'), FALSE),
			array('keyNotExists', array(array('key' => NULL), 'foo'), TRUE),
			array('count', array(array(0, 1, 2), 3), TRUE),
			array('count', array(array(0, 1, 2), 2), FALSE),
			array('uuid', array('00000000-0000-0000-0000-000000000000'), TRUE),
			array('uuid', array('ff6f8cb0-c57d-21e1-9b21-0800200c9a66'), TRUE),
			array('uuid', array('ff6f8cb0-c57d-11e1-9b21-0800200c9a66'), TRUE),
			array('uuid', array('ff6f8cb0-c57d-31e1-9b21-0800200c9a66'), TRUE),
			array('uuid', array('ff6f8cb0-c57d-41e1-9b21-0800200c9a66'), TRUE),
			array('uuid', array('ff6f8cb0-c57d-51e1-9b21-0800200c9a66'), TRUE),
			array('uuid', array('FF6F8CB0-C57D-11E1-9B21-0800200C9A66'), TRUE),
			array('uuid', array('zf6f8cb0-c57d-11e1-9b21-0800200c9a66'), FALSE),
			array('uuid', array('af6f8cb0c57d11e19b210800200c9a66'), FALSE),
			array('uuid', array('ff6f8cb0-c57da-51e1-9b21-0800200c9a66'), FALSE),
			array('uuid', array('af6f8cb-c57d-11e1-9b21-0800200c9a66'), FALSE),
			array('uuid', array('3f6f8cb0-c57d-11e1-9b21-0800200c9a6'), FALSE),
			array('throws', array(function ()
			                      {
				                      throw new LogicException('test');
			                      }, 'LogicException'), TRUE),
			array('throws', array(function ()
			                      {
				                      throw new LogicException('test');
			                      }, 'IllogicException'), FALSE),
			array('throws', array(function ()
			                      {
				                      throw new Exception('test');
			                      }), TRUE),
			array('throws', array(function ()
			                      {
				                      trigger_error('test');
			                      }, 'Throwable'), TRUE, FALSE, 70000),
			array('throws', array(function ()
			                      {
				                      trigger_error('test');
			                      }, 'Unthrowable'), FALSE, FALSE, 70000),
			array('throws', array(function ()
			                      {
				                      throw new Error();
			                      }, 'Throwable'), TRUE, TRUE, 70000),
		);
	}

	public function getMethods()
	{
		$methods = array();

		foreach ($this->getTests() as $params)
		{
			$methods[$params[0]] = array($params[0]);
		}

		return array_values($methods);
	}

	/**
	 * @dataProvider getTests
	 */
	public function testAssert($method, $args, $success, $multibyte = FALSE, $minVersion = NULL)
	{
		if ($minVersion && PHP_VERSION_ID < $minVersion)
		{
			$this->markTestSkipped(sprintf('This test requires php %s or upper.', $minVersion));

			return;
		}
		if ($multibyte && !function_exists('mb_strlen'))
		{
			$this->markTestSkipped('The function mb_strlen() is not available');

			return;
		}

		if (!$success)
		{
			$this->setExpectedException('\InvalidArgumentException');
		}

		call_user_func_array(array('Webmozart\Assert\Assert', $method), $args);
	}

	/**
	 * @dataProvider getTests
	 */
	public function testNullOr($method, $args, $success, $multibyte = FALSE, $minVersion = NULL)
	{
		if ($minVersion && PHP_VERSION_ID < $minVersion)
		{
			$this->markTestSkipped(sprintf('This test requires php %s or upper.', $minVersion));

			return;
		}
		if ($multibyte && !function_exists('mb_strlen'))
		{
			$this->markTestSkipped('The function mb_strlen() is not available');

			return;
		}

		if (!$success && NULL !== reset($args))
		{
			$this->setExpectedException('\InvalidArgumentException');
		}

		call_user_func_array(array('Webmozart\Assert\Assert', 'nullOr' . ucfirst($method)), $args);
	}

	/**
	 * @dataProvider getMethods
	 */
	public function testNullOrAcceptsNull($method)
	{
		call_user_func(array('Webmozart\Assert\Assert', 'nullOr' . ucfirst($method)), NULL);
	}

	/**
	 * @dataProvider getTests
	 */
	public function testAllArray($method, $args, $success, $multibyte = FALSE, $minVersion = NULL)
	{
		if ($minVersion && PHP_VERSION_ID < $minVersion)
		{
			$this->markTestSkipped(sprintf('This test requires php %s or upper.', $minVersion));

			return;
		}
		if ($multibyte && !function_exists('mb_strlen'))
		{
			$this->markTestSkipped('The function mb_strlen() is not available');

			return;
		}

		if (!$success)
		{
			$this->setExpectedException('\InvalidArgumentException');
		}

		$arg = array_shift($args);
		array_unshift($args, array($arg));

		call_user_func_array(array('Webmozart\Assert\Assert', 'all' . ucfirst($method)), $args);
	}

	/**
	 * @dataProvider getTests
	 */
	public function testAllTraversable($method, $args, $success, $multibyte = FALSE, $minVersion = NULL)
	{
		if ($minVersion && PHP_VERSION_ID < $minVersion)
		{
			$this->markTestSkipped(sprintf('This test requires php %s or upper.', $minVersion));

			return;
		}
		if ($multibyte && !function_exists('mb_strlen'))
		{
			$this->markTestSkipped('The function mb_strlen() is not available');

			return;
		}

		if (!$success)
		{
			$this->setExpectedException('\InvalidArgumentException');
		}

		$arg = array_shift($args);
		array_unshift($args, new ArrayIterator(array($arg)));

		call_user_func_array(array('Webmozart\Assert\Assert', 'all' . ucfirst($method)), $args);
	}

	public function getStringConversions()
	{
		return array(
			array('integer', array('foobar'), 'Expected an integer. Got: string'),
			array('string', array(1), 'Expected a string. Got: integer'),
			array('string', array(TRUE), 'Expected a string. Got: boolean'),
			array('string', array(NULL), 'Expected a string. Got: NULL'),
			array('string', array(array()), 'Expected a string. Got: array'),
			array('string', array(new stdClass()), 'Expected a string. Got: stdClass'),
			array('string', array(self::getResource()), 'Expected a string. Got: resource'),

			array('eq', array('1', '2'), 'Expected a value equal to "2". Got: "1"'),
			array('eq', array(1, 2), 'Expected a value equal to 2. Got: 1'),
			array('eq', array(TRUE, FALSE), 'Expected a value equal to false. Got: true'),
			array('eq', array(TRUE, NULL), 'Expected a value equal to null. Got: true'),
			array('eq', array(NULL, TRUE), 'Expected a value equal to true. Got: null'),
			array('eq', array(array(1), array(2)), 'Expected a value equal to array. Got: array'),
			array('eq', array(new ArrayIterator(array()), new stdClass()), 'Expected a value equal to stdClass. Got: ArrayIterator'),
			array('eq', array(1, self::getResource()), 'Expected a value equal to resource. Got: 1'),
		);
	}

	/**
	 * @dataProvider getStringConversions
	 */
	public function testConvertValuesToStrings($method, $args, $exceptionMessage)
	{
		$this->setExpectedException('\InvalidArgumentException', $exceptionMessage);

		call_user_func_array(array('Webmozart\Assert\Assert', $method), $args);
	}
}
