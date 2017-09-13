<?php
return [
	// Debugging
	'debug'                 => false,
	// Logging
    'log' => [
        'type' => 'File'
    ],

	// View
	'templates.path'        => './templates',
	'view'                  => '\Lee\View',

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
	// HTTP
	'http.version'          => '1.1',
	// Routing
	'routes.case_sensitive' => true,

	/* 默认设定 */
	'default_lang'          => 'zh-cn', // 默认语言
	'default_controller'    => 'Index', // 默认控制器名称
	'default_action'        => 'index', // 默认操作名称
	'default_charset'       => 'utf-8', // 默认输出编码
	'default_timezone'      => 'prc', // 默认时区

	'default_ajax_return'   => 'json', // 默认 ajax 数据返回格式
	'default_jsonp_handler' => 'jsonp_return', // 默认jsonp格式返回的处理方法
];
