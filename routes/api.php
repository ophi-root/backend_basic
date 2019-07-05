<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', ['middleware' => 'cors', 'namespace' => 'App\\Api\\V1\\Controllers'], function (Router $api) {
});