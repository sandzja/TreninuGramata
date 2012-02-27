<?php
/**
 *
 * @author tobre
 * @version 
 */
/**
 * DisableView Action Helper 
 * 
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_DisableLayout extends Zend_Controller_Action_Helper_Abstract {

	public function __construct() {
	}
	
	public function direct() {
		$this->_actionController->getHelper('layout')->disableLayout();
	}

}

