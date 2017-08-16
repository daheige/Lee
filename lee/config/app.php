<?php
return [
	// Debugging
	'debug'                 => true,
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
	'DEFAULT_LANG'          => 'zh-cn', // 默认语言
	'DEFAULT_CONTROLLER'    => 'Index', // 默认控制器名称
	'DEFAULT_ACTION'        => 'index', // 默认操作名称
	'DEFAULT_CHARSET'       => 'utf-8', // 默认输出编码
	'DEFAULT_TIMEZONE'      => 'PRC', // 默认时区
	'DEFAULT_AJAX_RETURN'   => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
	'DEFAULT_JSONP_HANDLER' => 'jsonpReturn', // 默认JSONP格式返回的处理方法
	'DEFAULT_FILTER'        => 'htmlspecialchars', // 默认参数过滤方法 用于I函数...
];
