<?php
//Routes

//var_dump($_SERVER);

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