<?php

namespace WowzaEC2;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ControllerProvider implements ControllerProviderInterface {
	
	public function connect(Application $app) {
		
		$controllers = $app['controllers_factory'];
		
		$controllers->get('/', 'WowzaEC2\Controller::statusAction');
		$controllers->get('/start', 'WowzaEC2\Controller::startAction');
		$controllers->get('/stop', 'WowzaEC2\Controller::stopAction');
		
		return $controllers;
	}
}
	
?>