<?php

$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/../../../../../', 'eden.env');
$dotEnv->load();

return [
	'settings' => [
		'ISSUER'                 => $_ENV['APP_ISSUER'],
		'AUDIENCES_HOST'         => $_ENV['APP_AUDIENCES_HOST'],
		'ALGORITHMS'             => $_ENV['APP_ALGORITHMS'],
		'displayErrorDetails'    => $_ENV['APP_DISPLAYERRORDETAILS'],
		'addContentLengthHeader' => $_ENV['APP_ADDCONTENTLENGTHHEADER'], // Allow the web server to send the content-length header
		'VERSION'                => $_ENV['APP_VERSION'],
		'BUILD'                  => $_ENV['APP_BUILD'],

		// Renderer settings
		'renderer'               => [
			'template_path' => __DIR__ . '/../templates/',
		],

		// Monolog settings
		'logger'                 => [
			'name'  => 'members',
			'path'  => __DIR__ . $_ENV['LOG_PATH'] . 'members.log',
			'level' => $_ENV['LOG_LEVEL'],
		],

		// Database settings
		'db' => [
			'dns' => $_ENV['DB_CONNECTION'] . ':host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'] . ';port=' . $_ENV['DB_PORT'],
			'username' => $_ENV['DB_USERNAME'],
			'password' => $_ENV['DB_PASSWORD'],
		],

	    // Curl Settings
		'curl' => [
			'host' => $_ENV['CURL_HOST'],
			'port' => $_ENV['CURL_PORT'],
		    'certPath' => $_ENV['CURL_CERTPATH'],
		    'certFileName' => $_ENV['CURL_CERTFILENAME'],
		],
	],
];
