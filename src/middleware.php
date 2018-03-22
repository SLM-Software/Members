<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Helpers\Cache\FileSystemCacheHandler;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;

class Middleware
{
	protected $container;

	public function __construct($container)
	{
		$this->container = $container;
		$this->container->logger->debug(__METHOD__);
	}

	/**
	 * Short Description
	 *
	 * Long Description
	 *
	 */
	public function __invoke($request, $response, $next)
	{
		$this->container->logger->debug(__METHOD__);

		$headers = getallheaders();
		$myResponse = $response;

		if ($_SERVER[SERVER_NAME] == 'localhost')
		{
			$audiences = "https://$_SERVER[SERVER_NAME]/edeninfo";
		} else
		{
			$audiences = "https://$_SERVER[HTTP_HOST]/edeninfo";
		}
		$this->container->logger->debug("\$audiences=$audiences");

		try
		{
			$verifier = new JWTVerifier(['valid_audiences' => [$audiences],
			                             'authorized_iss'  => [$_ENV['APP_ISSUER']],
			                             'supported_algs'  => [$_ENV['APP_ALGORITHMS']]
			]);
			$verifier->verifyAndDecode($headers['Authorization']);
		} catch (InvalidTokenException $e)
		{
			return $myResponse->withJson(array('errCode'    => 401,
			                                   'statusText' => $e->getMessage(),
			                                   'codeLoc'    => __METHOD__,
			                                   'custMsg'    => '',
			                                   'retPack'    => array('token' => $headers['Authorization'])));
		} catch (CoreException $e)
		{
			return $myResponse->withJson(array('errCode'    => 401,
			                                   'statusText' => $e->getMessage(),
			                                   'codeLoc'    => __METHOD__,
			                                   'custMsg'    => '',
			                                   'retPack'    => array('token' => $headers['Authorization'])));
		}

		$myResponse = $next($request, $response);

		return $myResponse;
	}
}