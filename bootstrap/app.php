<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Ensure mcrypt constants are defined even if mcrypt extension is not loaded
if (!extension_loaded('mcrypt')) {
    define('MCRYPT_MODE_CBC', 0);
    define('MCRYPT_RIJNDAEL_256', 0);
}

/*
|--------------------------------------------------------------------------
|  默认时区
|--------------------------------------------------------------------------
| 默认时间设置为上海
*/
date_default_timezone_set('Asia/Shanghai');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
 */

$app = new \Lee\Application(
    realpath(__DIR__ . '/../')
);

// 插件载入
$app->hook('lee.before', 'load_plugins');

$app->hook('lee.after', function() {
    app()->log()->save();
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
 */

// $app->middleware([
//    App\Http\Middleware\AuthMiddleware::class
// ]);

require __DIR__ . '/functions.php';

require __DIR__. '/../app/routes.php';

$app->run();
