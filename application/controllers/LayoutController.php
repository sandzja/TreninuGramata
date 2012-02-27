<?php

use Service\ServiceManager;
class LayoutController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function init() {
		$this->userService = ServiceManager::factory(new \Service\User());
		
		try{
			$this->view->activeMenuName = Zend_Registry::get('activeMenu');
		}catch(Zend_Exception $e){
		
		}
	}
	
	public function showTopMenuAction() {
		
		
	}
	
	public function headerAction() {
		
	}
}