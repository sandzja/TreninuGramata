<?php

class Zend_Controller_Action_Helper_SetActiveMenu extends Zend_Controller_Action_Helper_Abstract {
	
	public function __construct() {
	}
	
	public function direct($activeMenuLinkName) {
		Zend_Registry::set('activeMenu', $activeMenuLinkName);
	}
	
}