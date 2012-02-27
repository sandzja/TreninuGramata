<?php

use Service\ServiceManager;
class ChallengeController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\Challenge
	 */
	private $challengeService;
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function init() {
		$this->challengeService = ServiceManager::factory(new \Service\Challenge());
		$this->workoutService = ServiceManager::factory(new \Service\Workout());
		$this->userService = ServiceManager::factory(new \Service\User());
		
		$this->_helper->setActiveMenu('challenge');
	}
	
	public function indexAction() {
		$this->view->friendName = $this->_getParam('friendName');
		$this->view->type = $this->_getParam('type', 'all');
		$this->view->currentUser = $this->userService->getCurrentUser();
		$this->view->workoutService = $this->workoutService;
	}
	
	public function postsAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	if ($this->_getParam('type') == 'challenged-me') {
    		$challenges = $this->challengeService->getChallengedUser($this->userService->getCurrentUser(), 10, $this->_getParam('page', 0) * 10);
    	} else if ($this->_getParam('type') == 'i-challenged') {
    		$challenges = $this->challengeService->getUserChallenged($this->userService->getCurrentUser(), 10, $this->_getParam('page', 0) * 10);
    	} else {
    		$challenges = $this->challengeService->getAllChallenges(10, $this->_getParam('page', 0) * 10);
    	}
    	
    	$this->view->challenges = $challenges;
    }
	
}