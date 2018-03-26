<?php
//Routes

// For Testing only
//var_dump($_SERVER);

$app->group('', function(){
	$this->get('/members/activatemember', function ($request, $response, $args)
	{
		$this->logger->info("/activatemember '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->activateMember($request));
	});
	$this->get('/members/confirmmember', function ($request, $response, $args)
	{
		$this->logger->info("/confirmmember '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->confirmMember($request));
	});
	$this->get('/members/createmember', function ($request, $response, $args)
	{
		$this->logger->info("/createmember '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->createMember($request));
	});
	$this->get('/members/deactivatemember', function ($request, $response, $args)
	{
		$this->logger->info("/deactivatemember '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->deactivateMember($request));
	});
	$this->get('/members/ismember', function ($request, $response, $args)
	{
		$this->logger->info("/ismember '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->isMember($request));
	});
	$this->get('/members/ismemberactive', function ($request, $response, $args)
	{
		$this->logger->info("/ismemberactive '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->isMemberActive($request));
	});
	$this->get('/members/ismemberconfirmed', function ($request, $response, $args)
	{
		$this->logger->info("/ismemberconfirmed '/' route");

		// Creating Class instance
		$myMember = new \API\CreateMembers($this->logger, $this->db, $this->get('settings')['curl']);

		// Returning $body
		return $response->withJson($myMember->isMemberConfirmed($request));
	});
	$this->get('/members/readall', function ($request, $response, $args)
	{
		$this->logger->info("/readall '/' route");

		$memberDB = $this->db;
		$queryMembers = $memberDB->prepare("select * from slm.member limit 10");
		$members = '';
		try
		{
			$queryMembers->execute();
			$members = $queryMembers->fetchAll();
		} catch (PDOException $e)
		{
			$members = $e;
		}
		$this->logger->info(serialize($members));

		$body = $response->getBody();
		$body->write(serialize($members));
		return $response->withBody($body);
	});
	$this->get('/members/version', function ($request, $response, $args)
	{
		$this->logger->info("version '/' route");
		$versionSetting = $this->get('settings')['VERSION'];
		$buildSetting = $this->get('settings')['BUILD'];
		$myMembers = new \API\Members($this->logger, $this->db, $versionSetting, $buildSetting);

		return $response->withJson($myMembers->getVersion());
	});
})->add(new Middleware($container));

