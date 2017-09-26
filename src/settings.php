<?php
return [
	'settings' => [
		'displayErrorDetails'    => TRUE, // set to false in production
		'addContentLengthHeader' => FALSE, // Allow the web server to send the content-length header

		// Renderer settings
		'renderer'               => [
			'template_path' => __DIR__ . '/../templates/',
		],

		// Monolog settings
		'logger'                 => [
			'name'  => 'members',
			'path'  => __DIR__ . '/../logs/app.log',
			'level' => \Monolog\Logger::DEBUG,
		],

		// Database settings
		'db' => [
			'dns' => 'pgsql:host=localhost;dbname=slmdb;port=5432',
			'username' => 'slmadmin',
			'password' => 'Yidiao01',
		],
	],
];
