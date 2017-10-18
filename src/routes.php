<?php
//Routes

// For Testing only
//var_dump($_SERVER);

$app->get('/slm/api/members/activatemember', function ($request, $response, $args)
{
	$this->logger->info("/activatemember '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->activateMember($request));
});

$app->get('/slm/api/members/ismemberactive', function ($request, $response, $args)
{
	$this->logger->info("/ismemberactive '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->isMemberActive($request));
});

$app->get('/slm/api/members/ismember', function ($request, $response, $args)
{
	$this->logger->info("/ismember '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->isMember($request));
});

$app->get('/slm/api/members/confirmmember', function ($request, $response, $args)
{
	$this->logger->info("/confirmmember '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->confirmMember($request));
});

$app->get('/slm/api/members/ismemberconfirmed', function ($request, $response, $args)
{
	$this->logger->info("/ismemberconfirmed '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->isMemberConfirmed($request));
});

$app->get('/slm/api/members/createmember', function ($request, $response, $args)
{
	$this->logger->info("/createmember '/' route");

	// Creating Class instance
	$myMember = new \API\CreateMembers($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->createMember($request));
});

$app->get('/slm/api/members/readall', function ($request, $response, $args)
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

$app->get('/slm/api/members/version', function ($request, $response, $args)
{
	$this->logger->info("version '/' route");
	$myMembers = new \API\Members($this->logger);

	return $response->withJson($myMembers->getVersion());
});