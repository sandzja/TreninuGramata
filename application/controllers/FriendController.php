<?php

use Service\ServiceManager;
class FriendController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	/**
	 * @var \Service\Workout
	 */
	private $workoutService;
	
	public function init() {
		$this->userService = ServiceManager::factory(new \Service\User());
		$this->workoutService = ServiceManager::factory(new \Service\Workout());
		
		$this->_helper->setActiveMenu('friend');
    }
	
	public function indexAction() {
		$this->view->type = $this->_getParam('type', 'following');
		$this->view->name = $this->_getParam('name');
		$this->view->currentUser = $this->userService->getCurrentUser();
	}
	
	public function postsAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	        $this->_helper->disableLayout();
	    }
	    
	    if ($this->_getParam('name') == null) {
		    if ($this->_getParam('type') == 'following') {
		        $friends = $this->userService->getCurrentUser()->getFollowings()->slice($this->_getParam('page') * 10, 10);
		    } else if ($this->_getParam('type') == 'all-users') {
		        $friends = $this->userService->getAllUsers(10, $this->_getParam('page') * 10);
		    } else {
		        $friends = $this->userService->getCurrentUser()->getFollowers()->slice($this->_getParam('page') * 10, 10);
		    }
	    } else {
	        if ($this->_getParam('type') == 'following') {
	        	$friends = $this->userService->searchFollowing($this->_getParam('name'), 10, $this->_getParam('page') * 10);
	        } else if ($this->_getParam('type') == 'all-users') {
	        	$friends = $this->userService->searchUsers($this->_getParam('name'), 10, $this->_getParam('page') * 10);
	        } else {
	        	$friends = $this->userService->searchFollowers($this->_getParam('name'), 10, $this->_getParam('page') * 10);
	        }
	    }
	    
	    $this->view->users = $friends;
	    $this->view->currentUser = $this->userService->getCurrentUser();
	    $this->view->name = $this->_getParam('name');
	    $this->view->type = $this->_getParam('type');
	}

	public function profileAction() {
		$this->_forward('profile', 'user');
	}
}