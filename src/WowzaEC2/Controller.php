<?php

namespace WowzaEC2;

use Aws\Common\Aws;
use Silex\Application;

define(OFFLINE, 'offline');
define(PENDING, 'pending');
define(RUNNING, 'running');
define(STOPPING, 'shutting-down');

class Controller {
	
	private $aws;
	private $ec2;
	private $StatusResponse;
	
	
	// Create the Amazon API instances and query the server's status
	function __construct() {
		$this->aws = Aws::factory(CONFDIR.'/aws_credentials.php');
		$this->ec2 = $this->aws->get('Ec2');
		$this->StatusResponse = $this->ec2->describeInstances(
			array(
				'Filters' => array(
					array(
						'Name' => 'instance-state-name',
						'Values' => array(
							PENDING,
							RUNNING,
							STOPPING
						)
					)
				)
			)
		);
	}
	
	// Get the Server Status from the StatusResponse
	private function getStatus() {
		$status = OFFLINE;
		
		// Get and return status
		if ( isset ($this->StatusResponse['Reservations'][0]['Instances'][0]['State']['Name']) ) {	
			$status = $this->StatusResponse['Reservations'][0]['Instances'][0]['State']['Name'];
		}
		
		return $status;
	}
	
	// Output related stuff - JSON formatters
	private function json(Application $app, $message, $success) {
		$response = array('success' => $success);
		if ( $success ) {
			$response['status'] = $message;
		} else {
			$response['error'] = $message;
		}
		return $app->json($response);
	}
	
	private function success(Application $app, $status) {
		return $this->json($app, $status, true);
	}
	
	private function error(Application $app, $error) {
		return $this->json($app, $error, false);
	}
	
	// Display the Server Status
	public function statusAction(Application $app) {
		return $this->success($app, $this->getStatus());
	}
	
	
	// Start the EC2 Server Instance
	public function startAction(Application $app) {
		
		if ( $this->getStatus() == OFFLINE ) {
			
			$this->ec2->runInstances(array(
				'ImageId' => $app['wowza_ami'],
				'MinCount' => 1,
				'MaxCount' => 1	
			));
			
			return $this->success($app, PENDING);
			
		} else {
			
			return $this->error($app, 'There is already an instance '.$this->getStatus());
		}
		
	}
	
	
	// Stop the EC2 Server Instance
	public function stopAction(Application $app) {
		
		// Only stop if the instance is running
		if ( $this->getStatus() == RUNNING ) {
			
			// Terminate instance
			$this->ec2->terminateInstances(
				array(
					'InstanceIds' => array(
						$this->StatusResponse['Reservations'][0]['Instances'][0]['InstanceId']
					)
				)
			);
			
			return $this->success($app, STOPPING);
			
		} else {
			return $this->error($app, 'No instance is in running state.');
		}
	}
	
}	
	
	
?>