<?php


class CreateMembersTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

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

	protected function _afterSuite()
	{
	}

	// tests
	public function testCreateMemberWithNoValues_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Invalid primary email');
		$this->logger->debug('test has been run');
	}

	public function testCreateMemberWithPemail_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember?pemail=syacko@spotlightmart.com&');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Invalid primary phone');
		$this->logger->debug('test has been run');
	}

	public function testCreateMemberWithPemailAndPphone_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember?pemail=syacko@spotlightmart.com&pphone=6504830648&');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Invalid first name');
		$this->logger->debug('test has been run');
	}

	public function testCreateMemberWithPemailPphoneAndFname_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember?pemail=syacko@spotlightmart.com&pphone=6504830648&fname=Scott&');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Invalid last name');
		$this->logger->debug('test has been run');
	}

	public function testCreateMemberWithPemailPphoneFnameAndLname_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember?pemail=syacko@spotlightmart.com&pphone=6504830648&fname=Scott&lname=Yacko&');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Missing password');
		$this->logger->debug('test has been run');
	}

	public function testCreateMember_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/createmember?pemail=syacko@spotlightmart.com&pphone=6504830648&fname=Scott&lname=Yacko&pword=testaccount&');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Success');
		$this->logger->debug('test has been run');
	}

	public function testActivateMember_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/activatemember?pemail=syacko@spotlightmart.com');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Success');
		$this->logger->debug('test has been run');
	}
	public function testIsMemberActive_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/ismemberactive?pemail=syacko@spotlightmart.com');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->retPack->activemember == TRUE);
		$this->logger->debug('test has been run');
	}

	public function testConfirmMember_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/confirmmember?pemail=syacko@spotlightmart.com');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->statusText == 'Success');
		$this->logger->debug('test has been run');
	}

	public function testIsMemberConfirmed_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/ismemberconfirmed?pemail=syacko@spotlightmart.com');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->retPack->confirmed == TRUE);
		$this->logger->debug('test has been run');
	}

	public function testIsMember_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/ismember?pemail=syacko@spotlightmart.com');
		$myObj = json_decode($res->getBody());
		codecept_debug($myObj);
		$this->assertTrue($myObj->retPack->exists == TRUE);
		$this->logger->debug('test has been run');
	}

	public function testIsMemberNegative_Request()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'members/ismember?pemail=NOTFOUND@spotlightmart.com');
		$myArray = json_decode($res->getBody());
		codecept_debug($myArray);
		$this->assertTrue($myArray['retPack']['exists'] == FALSE);
		$this->logger->debug('test has been run');
	}

	public function testDeleteMember()
	{
		// This is not a REST API - It is for internal use only.
		//
		$myMember = new \API\CreateMembers($this->logger, $this->pdo);
		$myMember->deleteMember('syacko@spotlightmart.com');
	}
}