<?php

use Application\Http\HttpController;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;

require_once "Application/Vendor/autoload.php";

$http = new HttpController();

SimpleRouter::match(['get', 'post'], '/', function() use ($http)
{
    $http->handleMainpage();
});

SimpleRouter::match(['get', 'post'], '/dashboard', function() use ($http)
{
    $http->handleDashboard();
});

SimpleRouter::post('/upload', function() use ($http)
{
    return $http->upload($_FILES, $_POST);
});

SimpleRouter::get('/upload', function()
{
    return ':)';
});

SimpleRouter::get('/json/{json}', function($json) use ($http)
{
    return $http->returnJson($json);
})->where(['json' => '[\d]{1,6}']);

SimpleRouter::get('/bio/{username}', function($username) use ($http)
{
    $http->handleBioPage($username);
})->where(['username' => '[\w]{3,16}']);

SimpleRouter::get('/image/{image}', function($image) use ($http)
{
    $http->showImage($image);
})->where(['image' => '[a-zA-Z0-9]{6}']);

SimpleRouter::error(function(Request $request, \Exception $exception) use ($http)
{
    switch($exception->getCode()) {
        case 404:
            $http->showError();
            break;
        default:
            $http->showError(500);

    }
});

SimpleRouter::start();

