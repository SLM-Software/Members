<?php
/**
 * CreateMember is an api that extends Members.
 *
 * This API will create a member in the database.
 *
 */

/**
 * CreateMember will validate the input values and store a row in the database.
 *
 *  The methods in this class will parse values passed, validate that they meet requirements, store
 * the values in the database and handle errors that may occur.
 *
 */
class CreateMember extends Members
{
	/**
	 * @var string $myDeviceId This is a unique number create by the mobile app on the device (Required)
	 */
	protected $myDeviceId;
	/**
	 * @var string $myPrimaryEmail This is the email used to log into SLM systems (Required)
	 */
	protected $myPrimaryEmail;
	/**
	 * @var integer $myPrimaryPhone This is the phone for the device that installed the app upto 10 digits (Required)
	 */
	protected $myPrimaryPhone;
	/**
	 * @var string $myFirstName This is the persons first name (Required)
	 */
	protected $myFirstName;
	/**
	 * @var string $myLastName This is the persons last name (Required)
	 */
	protected $myLastName;
	/**
	 * @var string $myPassword This is the password the member uses to log in (Required)
	 */
	protected $myPassword;
	/**
	 * @var boolean $myConfirmed This is set when the primary email account has been confirmed
	 */
	protected $myConfirmed;
	/**
	 * @var json $myUpdatedBy This is a json string for the type, timestamp and user id last changing the row
	 */
	protected $myUpdatedBy;
	/**
	 * @var json $myPrimaryPaymentMethod This is a json string for the primary means of payment
	 */
	protected $myPrimaryPaymentMethod;
	/**
	 * @var string $mySuppliedGender This is the gender the person  assigned to themselves
	 */
	protected $mySuppliedGender;
	/**
	 * @var string $myPredictedGender This is the gender SLM systems believes the person to be
	 */
	protected $myPredictedGender;
	/**
	 * @var string $myBirthdate This is a json string with the day, month and/or year of the member
	 */
	protected $myBirthdate;
	/**
	 * @var "Slim\Http\RequestMonolog\Logger" $logger The instance of the Logger created at startup.
	 *      (Disregard the leading "\API\")
	 */

	/**
	 * This will create an instance of a customer in the database
	 *
	 * This takes the information supplied and creates a row in the database for that member
	 *
	 * @api
	 *
	 * @param ServerRequestInterface $request The request object implements the PSR 7
	 *                                        ServerRequestInterface with which you can inspect and
	 *                                        manipulate the HTTP request method, headers, and body.
	 *
	 *          The query elements in the URI are as follow:
	 *          Required elements:
	 *              did       = Device Id [varchar(30)]
	 *              pemail    = primary email [varchar(100)]
	 *              pphone    = primary phone [bigint]
	 *              fname     = first name [varchar(25)]
	 *              lname     = last name [varchar(30)]
	 *              pword     = password (Must be hashed by caller) [varchar(100)]
	 *
	 *          Option elements:
	 *              ppmethod  = primary payment method [json]
	 *              sgender   = supplied gender [char(6)]
	 *              bdate     = birthdate [json]
	 *
	 * @todo Determine the json format for the payment method
	 * @todo Code ppmethod
	 * @todo Code sgender
	 * @todo code bdate
	 * @todo Determine the json format for updateBy
	 * @todo build interface for predicted gender
	 * @todo Determine the json format for birthdate
	 *
	 * @return array  Keys: errCode, errText, errLoc
	 */
	public function createmember($request)
	{
		$this->myLogger->debug(__METHOD__);

		// Getting the Query Paramters
		$this->myLogger->debug("getUri / " . $request->getUri());
		$this->myLogger->debug("getQueryParams: " . implode("", $request->getQueryParam('fname')));

		$resultString = $this->setDeviceId($request->getQueryParam('did'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		$this->myLogger->info("getQueryParam / pemail:" . $request->getQueryParam('pemail'));
		$resultString = $this->setPrimaryEmail($request->getQueryParam('pemail'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		$this->myLogger->info("getQueryParam / pphone:" . $request->getQueryParam('pphone'));
		$resultString = $this->setPrimaryPhone($request->getQueryParam('pphone'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		$this->myLogger->info("getQueryParam / fname:" . $request->getQueryParam('fname'));
		$resultString = $this->setFirstName($request->getQueryParam('fname'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		$this->myLogger->info("getQueryParam / lname:" . $request->getQueryParam('lname'));
		$resultString = $this->setLastName($request->getQueryParam('lname'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		$this->myLogger->info("getQueryParam / pword:" . $request->getQueryParam('pword'));
		$resultString = $this->setPassword($request->getQueryParam('pword'));
		if ($resultString['errCode'] > 0)
		{
			return $resultString;
		}

		if ($resultString['errCode'] == 0)
		{
			$resultString = $this->saveMember();
		}

		return $resultString;
	}

	/**
	 *
	 */
	protected function saveMember()
	{
		$this->myLogger->debug(__METHOD__);
		$resultString = '';

		$mySTMT = $this->myDB->prepare('INSERT INTO slm.members (deviceid, primaryemail, primaryphone, firstname, lastname, password) VALUES (:did, :pemail, :pphone, :fname, :lname, :pword)');
		try
		{
			$mySTMT->execute(array(':did' => $this->myDeviceId, ':pemail' => $this->myPrimaryEmail, ':pphone' => $this->myPrimaryPhone, ':fname' => $this->myFirstName, ':lname' => $this->myLastName, ':pword' => $this->myPassword));
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} catch (PDOException $e)
		{
			$resultString = array('errCode' => 900, 'errText' => $e->getMessage(), 'errLoc' => __METHOD__);
		}

		return $resultString;
	}

	/**
	 * This will validate the assigned device id and save it.
	 *
	 * @param $did
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setDeviceId($did)
	{
		$this->myLogger->debug(__METHOD__);
		if (preg_match('/^\{?[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}\}?$/', trim($did)))
		{
			$this->myLogger->debug(__METHOD__ . '/ valid UUID');
			$this->myDeviceId = trim($did);
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} else
		{
			$this->myLogger->warning(__METHOD__ . '/ Invalid UUID XXXXXXXXXXXX: ' . trim($did));
			$resultString = array('errCode' => 900, 'errText' => 'Invalid UUID', 'errLoc' => __METHOD__);
		}
		return $resultString;
	}

	/**
	 * This will validate the primary email and save it.
	 *
	 * @param $pemail
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setPrimaryEmail($pemail)
	{
		$this->myLogger->debug(__METHOD__);
		if (filter_var(trim($pemail), FILTER_VALIDATE_EMAIL))
		{
			$this->myLogger->debug(__METHOD__ . '/ valid primary email');
			$this->myPrimaryEmail = trim($pemail);
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} else
		{
			$this->myLogger->warning(__METHOD__ . '/ Invalid priamary email XXXXXXXXXXXX: ' . trim($pemail));
			$resultString = array('errCode' => 900, 'errText' => 'Invalid primary email', 'errLoc' => __METHOD__);
		}
		return $resultString;
	}

	/**
	 * This will validate the phone number and save it.
	 *
	 * @param $pphone
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setPrimaryPhone($pphone)
	{
		$this->myLogger->debug(__METHOD__);
		if (preg_match('/^[0-9]{10}$/', trim($pphone)))
		{
			$this->myLogger->debug(__METHOD__ . '/ valid primary phone');
			$this->myPrimaryPhone = trim($pphone);
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} else
		{
			$this->myLogger->warning(__METHOD__ . '/ Invalid primary phone XXXXXXXXXXXX: ' . trim($pphone));
			$resultString = array('errCode' => 900, 'errText' => 'Invalid primary phone', 'errLoc' => __METHOD__);
		}
		return $resultString;
	}

	/**
	 * This will validate the first name and save it.
	 *
	 * @param $fname
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setFirstName($fname)
	{
		$this->myLogger->debug(__METHOD__);
		if (preg_match('/^[a-zA-Z ]+$/i', trim($fname)))
		{
			$this->myLogger->debug(__METHOD__ . '/ valid first name');
			$this->myFirstName = trim($fname);
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} else
		{
			$this->myLogger->warning(__METHOD__ . '/ Invalid first name: ' . trim($fname));
			$resultString = array('errCode' => 900, 'errText' => 'Invalid first name', 'errLoc' => __METHOD__);
		}
		return $resultString;
	}

	/**
	 * This will validate the last name and save it
	 *
	 * @param $lname
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setLastName($lname)
	{
		$this->myLogger->debug(__METHOD__);
		if (preg_match('/^[a-zA-Z ]+$/i', trim($lname)))
		{
			$this->myLogger->debug(__METHOD__ . '/ valid last name');
			$this->myLastName = trim($lname);
			$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		} else
		{
			$this->myLogger->warning(__METHOD__ . '/ Invalid last name: ' . trim($lname));
			$resultString = array('errCode' => 900, 'errText' => 'Invalid last name', 'errLoc' => __METHOD__);
		}
		return $resultString;
	}

	/**
	 * This will save the password without any validation.
	 *
	 * @param $pword
	 *
	 * @return array Keys: errCode, errText, errLoc
	 */
	protected function setPassword($pword)
	{
		$this->myLogger->debug(__METHOD__);
		$this->myPassword = trim($pword);
		$resultString = array('errCode' => 0, 'errText' => 'Success', 'errLoc' => '');
		return $resultString;
	}

	/**
	 * CreateMember constructor.
	 *
	 * @param $logger
	 */
	public function __construct($logger, $db)
	{
		parent::__construct($logger, $db);
		$this->myLogger->debug(__METHOD__);
	}
}