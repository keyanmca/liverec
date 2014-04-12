<?php

// Classes to use
$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('WowzaEC2', __DIR__);

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// Configuration
require_once __DIR__.'/../config/liverec.php';

// Initialize the Application
$app = new Application();
$app['debug'] = DEBUG;
$app['wowza_ami'] = WOWZA_IMAGE_ID;

// Mount the WowzaEC2 API in the /server context
$app->mount('/server', new WowzaEC2\ControllerProvider());

// For now, only the server part is active, so redirect there
$app->get('/', function () use ($app) {
	return $app->redirect('/server');
});


$app->run();

?>