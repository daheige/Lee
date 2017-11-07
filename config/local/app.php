<?php
return [
	// Session
	'session' => [
		'hanlder' => 'Redis',
		'name'    => 'lee_sessionid',
		'expires' => 3600,
		'path'    => '/',
		'domain'  => '',
		'options' => [
			'host'       => '10.10.19.117',
			'port'       => 6379,
			'auth'       => false,
			'prefix'     => '',
			'timeout'    => false,
			'persistent' => false,
		],
	],
];
