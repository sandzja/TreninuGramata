<?php

class Zend_Controller_Action_Helper_GetEntityManager extends Zend_Controller_Action_Helper_Abstract {

	public function __construct() {
	}
	
	public function direct() {
		$doctrine = Zend_Registry::get('doctrine');
		return $doctrine->getEntityManager();
	}

}