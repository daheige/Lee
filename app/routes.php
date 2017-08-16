<?php
$app->router->group(['namespace' => '\\App\\Controllers\\Home'], function() use ($app) {
    $app->router->get('/', "IndexController@index");
});
