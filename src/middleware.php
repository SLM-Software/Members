<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

use Auth0\SDK\JWTVerifier;
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

		$audiences = "https://" . $this->container->get('settings')['AUDIENCES_HOST'] . "/members";
		$this->container->logger->debug("\$audiences=$audiences");

		try
		{
			$verifier = new JWTVerifier(['valid_audiences' => [$audiences],
			                             'authorized_iss'  => [$this->container->get('settings')['ISSUER']],
			                             'supported_algs'  => [$this->container->get('settings')['ALGORITHMS']]
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