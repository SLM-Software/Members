<?php
// Routes

$app->get('/createmember', function ($request, $response, $args) {
    $this->logger->info("/createmember '/' route");

    // Getting the Query Paramters
    $body = $response->getBody();
    $this->logger->info("getUri / " . $request->getUri());
    $this->logger->info("getQueryParams / " . implode("", $request->getQueryParams()));

    // TEST CODE
    $body->write(var_dump($request->getQueryParams()));

    // Creating Class instance
    $myMember = new \API\Members();
    $body->write($myMember->imHere());

    // Returning $body
    return $response->withBody($body);
});

$app->get('/readall', function ($request, $response, $args) {
    $this->logger->info("/readall '/' route");

    $memberDB = $this->db;
    $queryMembers = $memberDB->prepare("select * from slm.members limit 10");
    try{
        $queryMembers->execute();
        $members = $queryMembers->fetchAll();
    } catch (\PDOExeption $e) {
        $members = $e;
    }
    $this->logger->info(serialize($members));

    $body = $response->getBody();
    $body->write(serialize($members));
    return $response->withBody($body);
});

$app->get('/version', function ($request, $response, $args) {
    $this->logger->info("version '/' route");

    $body = $response->getBody();
    $body->write(file_get_contents('http://localhost/slm_api/slminfo.slm/version'));

    return $response->withBody($body);
});

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("catch-all '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
