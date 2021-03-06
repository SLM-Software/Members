<?php
/**
 * This is the class file that is the parent of all member based API's.
 *
 * API's for members, people who use SLM facilities, are contained here or in sub-class of Member
 * if the size and complexity of the API warrants it's own class.
 *
 */
namespace API;

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
	 * @var  $myVersionSettings This has version value.
	 */
	protected $myVersionSetting;

	/**
	 * @var  $myBuildSettings This has build number.
	 */
	protected $myBuildSetting;

	/**
	 * return the version of the API being called.
	 *
	 * @api
	 *
	 * @example https://{Domain}/slm/api/members/version This will return the version and the build.
	 *
	 * @return array Keys: errCode, statusText, codeLoc, custMsg, retPack
	 */
	public function getVersion()
	{
		return array('errCode' => 0,
		             'statusText' => '',
		             'codeLoc' => __METHOD__,
		             'custMsg' => '',
		             'retPack' => array('version' => $this->myVersionSetting, 'build' => $this->myBuildSetting));
	}

	/**
	 * Members constructor.
	 *
	 * @param $logger
	 * @param $db
	 * @param $versionSetting
	 * @param $buildSetting
	 *
	 */
	public function __construct($logger, $db, $versionSetting, $buildSetting)
	{
		$this->myLogger = $logger;
		$this->myLogger->debug(__METHOD__);

		$this->myDB = $db;
		$this->myVersionSetting = $versionSetting;
		$this->myBuildSetting = $buildSetting;
	}
}