<?php
/**
 * This is the class file that is the parent of all member based API's.
 *
 * API's for members, people who use SLM facilities, are contained here or in sub-class of Member
 * if the size and complexity of the API warrants it's own class.
 *
 */
namespace API;

use Slim;
/**
 * This is the Members parent class which contains non-complex methods.
 *
 *  Each API is either a method (function) in this class or a sub-call of Members.
 * There are no rules to determine if the API should be a method in this class or a sub-class
 * of members. If you feel that the method is to complex and should be refactored to a sub-class,
 * please do so!
 *
 * @param "Slim\Http\RequestMonolog\Logger" $logger The instance of the Logger created at startup.
 *
 */
class Members
{
	/**
	 * @var "Slim\Http\RequestMonolog\Logger" $logger The instance of the Logger created at startup.
	 */
	protected $myLogger;

	/**
	 * @var PDO $db The instance of the Postgresql PDO connect created at startup.
	 */
	protected $myDB;

	/**
	 * return the version of the API being called.
	 *
	 * @api
	 *
	 * @return array Keys: errCode, statusText, codeLoc, custMsg, retPack
	 */
	public function getVersion()
	{
		$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8080/slm/api/', 'timeout' => 2.0]);
		$res = $client->request('GET', 'slminfo/version');
		$retValue = substr($res->getBody(), 0);
		$myObj = json_decode($retValue);
		$resultString = array('errCode' => 0, 'statusText' => 'Success', 'codeLoc' => __METHOD__, 'custMsg' => '', 'retPack' => (array)$myObj->retPack);
		return $resultString;
	}

	/**
	 * Members constructor.
	 *
	 * @param $logger
	 */
	public function __construct($logger, $db)
	{
		$this->myLogger = $logger;
		$this->myLogger->debug(__METHOD__);

		$this->myDB = $db;
	}
}