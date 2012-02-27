<?php

use Service\Api\TrackPoint;
use Service\Api\Exercise;
use Service\Api\Workout;
use Service\Api\User;
use Service\Api\Friends;
use Service\Api\Login;

class IphoneController extends Zend_Controller_Action {
	
	private $version;
	
	public function init() {
		$this->version = $this->_getParam('version', '');
    }

    public function loginAction() {
		$this->_helper->disableView();
		
    	$api = new Mobi_Api();
		$api->setClass('\Service\Api' . $this->version . '\Login');
		$response = $api->handle();
    }
    
    public function userAction() {
		$this->_helper->disableView();
		
    	$api = new Mobi_Api();
		$api->setClass('\Service\Api' . $this->version . '\User');
		$response = $api->handle();
    }
    
	public function workoutAction() {
		$this->_helper->disableView();
		
    	$api = new Mobi_Api();
		$api->setClass('\Service\Api' . $this->version . '\Workout');
		$response = $api->handle();
    }
    
	public function exerciseAction() {
		$this->_helper->disableView();
		
    	$api = new Mobi_Api();
		$api->setClass('\Service\Api' . $this->version . '\Exercise');
		$response = $api->handle();
    }
    
    public function friendsAction() {
    	$this->_helper->disableView();
		
    	$api = new Mobi_Api();
		$api->setClass('\Service\Api' . $this->version . '\Friends');
		$response = $api->handle();
    }
    
    public function syncAction() {
    	$this->_helper->disableView();
    
    	$api = new Mobi_Api();
    	$api->setClass('\Service\Api' . $this->version . '\Sync');
    	$response = $api->handle();
    }
}
	
