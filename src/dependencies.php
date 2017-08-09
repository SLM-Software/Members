<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c)
{
	$settings = $c->get('settings')['renderer'];
	return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c)
{
	$settings = $c->get('settings')['logger'];
	$logger = new Monolog\Logger($settings['name']);
	$logger->pushProcessor(new Monolog\Processor\UidProcessor());
	$logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
	return $logger;
};

// database - postgres
$container['db'] = function ($c)
{
	$settings = $c->get('settings')['db'];
	$pdo = new PDO('pgsql:host=localhost;dbname=slmdb;port=5432', 'slmadmin', 'Yidiao01');
	$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
};

//unset($app->getContainer()['errorHandler']);
//unset($app->getContainer()['phpErrorHandler']);
/*$c['errorHandler'] = function ($c) {
	return function ($request, $response, $exception) use ($c) {
		return $c['response']->withStatus(500)
			->withHeader('Content-Type', 'text/html')
			->write('Something went wrong!');
	};
};*/
