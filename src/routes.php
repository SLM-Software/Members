<?php
//Routes

$app->get('/createmember', function ($request, $response, $args)
{
	$this->logger->info("/createmember '/' route");

	// Creating Class instance
	$myMember = new CreateMember($this->logger, $this->db);

	// Returning $body
	return $response->withJson($myMember->createmember($request));
});

$app->get('/readall', function ($request, $response, $args)
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

$app->get('/version', function ($request, $response, $args)
{
	$this->logger->info("version '/' route");

	$body = $response->getBody();
	$body->write(file_get_contents('http://localhost/slm_api/slminfo.slm/version'));

	return $response->withBody($body);
});

$app->get('/[{name}]', function ($request, $response, $args)
{
	// Sample log message
	$this->logger->info("catch-all '/' route");

	// Render index view
	return $this->renderer->render($response, 'index.phtml', $args);
});
