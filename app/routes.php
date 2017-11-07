<?php
$app->router->group(['namespace' => '\\App\\Controllers\\Home'], function() use ($app) {
    $app->router->get('/', "Index@index");
});

$app->router->group(['namespace' => '\\App\\Controllers\\Admin', 'prefix' => '/admin'], function() use ($app) {
    $app->router->get('/', "Index@index");
});
