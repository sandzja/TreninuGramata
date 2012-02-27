<?php

class Zend_Controller_Action_Helper_SetLayout extends Zend_Controller_Action_Helper_Abstract {

	public function __construct() {
	}
	
	public function direct($layout) {
		$this->_actionController->getHelper('layout')->setLayout($layout);
	}

}