<?php

class MembersTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \apiResults
	 */
	protected $apiResults;

	protected function _before()
	{
		require __DIR__ . '/../../vendor/autoload.php';

		// Instantiate the app
		$this->settings = require __DIR__ . '/../../src/settings.php';
		$app = new \Slim\App($this->settings);

		// Set up dependencies
		require __DIR__ . '/../../src/dependencies.php';

		// Register middleware
		require __DIR__ . '/../../src/middleware.php';

		// Register routes
		require __DIR__ . '/../../src/routes.php';

		// Start Logger
		$this->logger = new Monolog\Logger($this->settings['settings']['logger']['name']);
		$this->logger->pushProcessor(new Monolog\Processor\UidProcessor());
		$this->logger->pushHandler(new Monolog\Handler\StreamHandler($this->settings['settings']['logger']['path'], $this->settings['settings']['logger']['level']));

		// Start Database
		$this->pdo = new PDO($this->settings['settings']['db']['dns'], $this->settings['settings']['db']['username'], $this->settings['settings']['db']['password']);
		$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	protected function _after()
	{
	}

	// tests
	public function testMembersTest()
	{
		codecept_debug('Starting testMembersTest - Executing getVersion:');
		$myMembers = new \API\Members($this->logger, $this->pdo, $this->settings['settings']['VERSION'], $this->settings['settings']['BUILD']);
		$this->apiResults = $myMembers->getVersion();
		$assertResult['TestVersion'] = $this->assertTrue($this->apiResults['retPack']['version'] == $this->settings['settings']['VERSION']);
		$assertResult['TestBuild'] = $this->assertTrue($this->apiResults['retPack']['build'] == $this->settings['settings']['BUILD']);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;
	}

	protected function displayAssertions($assertResult)
	{
		foreach ($assertResult as $key => $value)
		{
			if ($value == 0)
			{
				$resultDisplay = 'Passed';
			} else
			{
				$resultDisplay = 'Failed';
			}
			codecept_debug('-> Assertion[' . $key . '] ' . $resultDisplay);
		}
	}
}