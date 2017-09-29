<?php
return [
	// Debugging
	'debug'                 => false,

	// Logging
    'log' => [
        'type' => 'File'
    ],

	// Cookies
	'cookies'               => [
		'encrypt'     => false,
		'expires'     => 0,
		'path'        => '/',
		'domain'      => null,
		'secure'      => false,
		'httponly'    => false,
		// Encryption
		'secret_key'  => 'CHANGE_ME',
		'cipher'      => MCRYPT_RIJNDAEL_256,
		'cipher_mode' => MCRYPT_MODE_CBC,
	],

	// Routing
	'routes' => [
		'case_sensitive' => true
	],

	'default_ajax_return'   => 'json', // 默认 ajax 数据返回格式
	'default_jsonp_handler' => 'lee_callback', // 默认jsonp格式返回的处理方法
];
