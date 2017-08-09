<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A skipped test case
 */
class PHPUnit_Framework_SkippedTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	protected $message = '';

	/**
	 * @var bool
	 */
	protected $backupGlobals = FALSE;

	/**
	 * @var bool
	 */
	protected $backupStaticAttributes = FALSE;

	/**
	 * @var bool
	 */
	protected $runTestInSeparateProcess = FALSE;

	/**
	 * @var bool
	 */
	protected $useErrorHandler = FALSE;

	/**
	 * @var bool
	 */
	protected $useOutputBuffering = FALSE;

	/**
	 * @param string $message
	 */
	public function __construct($className, $methodName, $message = '')
	{
		$this->message = $message;
		parent::__construct($className . '::' . $methodName);
	}

	/**
	 * @throws PHPUnit_Framework_Exception
	 */
	protected function runTest()
	{
		$this->markTestSkipped($this->message);
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Returns a string representation of the test case.
	 *
	 * @return string
	 */
	public function toString()
	{
		return $this->getName();
	}
}
