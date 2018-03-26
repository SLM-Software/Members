<?php


class CreateMembersRESTTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \API Results
	 */
	protected $apiResults;

	/**
	 * @var \accessToken
	 */
	protected $token;

	protected function _before()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://spotlightmartdev.auth0.com/oauth/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\"client_id\":\"4b7312zrY5eeaU0zdNeBg5LxIoG7RiEz\",\"client_secret\":\"9f1_2mLdGKdetHVfMM_A95f5izfZa5_XcSgL2cNzzTngwZn25Pm-wkxH11ki5Rm_\",\"audience\":\"https://localhost/members\",\"grant_type\":\"client_credentials\"}",
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$this->token = json_decode($response)->access_token;
		}
	}

	protected function _after()
	{
	}

	// tests
	public function testCreateMembersRESTTest()
	{
		$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/../../../../../../', 'eden.env');
		$dotEnv->load();

		$headers = [
			'Authorization' => $this->token,
			'Accept'        => 'application/json',
			'Cache-Control' => 'no-cache',
		];

		$this->client = new \GuzzleHttp\Client(['base_uri' => 'https://' . $_ENV['CURL_HOST'] . ':' . $_ENV['CURL_PORT'], 'timeout' => 2.0]);

//      IS MEMBER (FALSE)
		codecept_debug('Starting testCreateMembersRESTTest - Executing members/ismember (Not member):');
		$res = $this->client->request('GET', 'members/ismember?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['retPack'] = $this->assertTrue($this->apiResults->retPack->exists == FALSE);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

//		CREATE MEMBER TESTS
		codecept_debug('->> Executing members/createmember (No Email):');
		$res = $this->client->request('GET', 'members/createmember', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 900);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Invalid primary email');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/createmember (Only Email - No phone):');
		$res = $this->client->request('GET', 'members/createmember?pemail=testmember@3denlounge.com&', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 900);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Invalid primary phone');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/createmember (Email, phone - No First Name):');
		$res = $this->client->request('GET', 'members/createmember?pemail=testmember@3denlounge.com&pphone=6504830648&', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 900);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Invalid first name');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/createmember (Email, phone, First Name - No Last Name):');
		$res = $this->client->request('GET', 'members/createmember?pemail=testmember@3denlounge.com&pphone=6504830648&fname=Scott&', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 900);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Invalid last name');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/createmember (Success):');
		$res = $this->client->request('GET', 'members/createmember?pemail=testmember@3denlounge.com&pphone=6504830648&fname=Scott&lname=Yacko&', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Success');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/createmember (Duplicate):');
		$res = $this->client->request('GET', 'members/createmember?pemail=testmember@3denlounge.com&pphone=6504830648&fname=Scott&lname=Yacko&', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 23505);
		$assertResult['statusText'] = $this->assertTrue(strpos($this->apiResults->statusText, '[23505]: Unique violation: 7 ERROR') > 0);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;
//		END OF CREATE MEMBER

//		IS MEMBER (TRUE)
		codecept_debug('->> Executing members/ismember:');
		$res = $this->client->request('GET', 'members/ismember?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->retPack->exists == TRUE);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;
//		END OF ISMEMBER

//		ACTIVATE/DEACTIVATE MEMBER
		codecept_debug('->> Executing members/activatemember:');
		$res = $this->client->request('GET', 'members/activatemember?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Success');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/ismemberactive (True):');
		$res = $this->client->request('GET', 'members/ismemberactive?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->retPack->activemember == TRUE);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/deactivatemember:');
		$res = $this->client->request('GET', 'members/activatemember?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Success');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/ismemberactive (False):');
		$res = $this->client->request('GET', 'members/ismemberactive?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Success');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;
//		END OF ACTIVATE MEMBER

//		CONFIRM MEMBER
		codecept_debug('->> Executing members/ismemberconfirmed (False):');
		$res = $this->client->request('GET', 'members/ismemberconfirmed?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['retPack'] = $this->assertTrue($this->apiResults->retPack->confirmed == FALSE);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/confirmmember:');
		$res = $this->client->request('GET', 'members/confirmmember?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['statusText'] = $this->assertTrue($this->apiResults->statusText == 'Success');
		$this->displayAssertions($assertResult);
		$assertResult = NULL;

		codecept_debug('->> Executing members/ismemberconfirmed (True):');
		$res = $this->client->request('GET', 'members/ismemberconfirmed?pemail=testmember@3denlounge.com', ['verify' => false, 'headers' => $headers]);
		$this->apiResults = json_decode($res->getBody());
		$assertResult['errCode'] = $this->assertTrue($this->apiResults->errCode == 0);
		$assertResult['retPack'] = $this->assertTrue($this->apiResults->retPack->confirmed == TRUE);
		$this->displayAssertions($assertResult);
		$assertResult = NULL;
//		END OF CONFIRM MEMBER


//		// This is not a REST API - It is for internal use only.
//		//
		if (strtoupper($_ENV['APP_ENV']) == 'LOCAL')
		{
			$myMember = new \API\CreateMembers($this->logger, $this->pdo);
			$myMember->deleteMember('testmember@3denlounge.com');
		}
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