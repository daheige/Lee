<?php
return [
    // Debugging
    'debug'                 => true,

    // Cookies
    'cookies'               => [
        'encrypt'     => true,
        'expires'     => 3600,
        'path'        => '/',
        'domain'      => 'localhost',
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

    /*
    |--------------------------------------------------------------------------
    |  默认语言
    |--------------------------------------------------------------------------
    */
    'default_lang'          => 'zh-cn',

    /*
    |--------------------------------------------------------------------------
    |  默认输出编码
    |--------------------------------------------------------------------------
    */
    'default_charset'       => 'utf-8', // 默认输出编码

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'aliases' => [
        // 'DB'                => 'Illuminate\Support\Facades\DB',
        'App'              => 'Lee\Application',
        'View'              => 'Lee\View',
        'Log' => 'Lee\Log',
        'Request' => 'Lee\Http\Request',
        'Response' => 'Lee\Http\Response',
        'Router' => 'Lee\Route\Router',
    ],

];
