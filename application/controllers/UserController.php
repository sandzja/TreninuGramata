<?php

use Service\ServiceManager;
class UserController extends Zend_Controller_Action {
	
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
    }

    public function loginAction() {
    	if ($this->_getParam('type') == 'facebook') {
    		if (!$this->userService->loginFacebook()) {
    			$this->_redirect($this->userService->getFacebookLoginUrl());
    		}
    	}
   		if ($this->_getParam('type') == 'twitter') {
    		if (!$this->userService->loginTwitter()) {
    			$this->_redirect($this->userService->getTwitterLoginUrl());
    		}
    	}
    }
    
    public function logoutAction() {
    	$this->userService->logout();
    	$this->_helper->redirector('index', 'index');
    }
    
    public function syncFriendsAction() {
    	$this->_helper->disableView();
    	$this->userService->updateFacebookData($this->_getParam('userId'));
    	$this->userService->syncTwitterFriends($this->_getParam('userId'));
    }
    
    public function homeAction() {
    	$this->_forward('index', 'news-feed');
    }
    
    public function leftMenuAction() {
    	$user = $this->userService->getCurrentUser();
    	$this->view->user = $user;
    	$this->view->workoutService = $this->workoutService;
    }
    
    public function overallTimeGraphAction() {
    	$this->_helper->disableLayout();
    	if ($this->userService->getCurrentUser() != null) {
    	
	    	$overallTimes = $this->userService->getOverallTimeGraph();
	    	
	    	$this->view->overallTimes = $overallTimes;
    	}
    }
    
    public function distanceGraphAction() {
    	$this->_helper->disableLayout();
    	
    	$distances = $this->userService->getDistancesGraph();
    	
    	$this->view->distances = $distances;
    }
    
    public function workoutsGraphAction() {
    	$this->_helper->disableLayout();
    	
    	$workouts = $this->userService->getWorkoutsGraph();
    	
    	$this->view->workouts = $workouts;
    }
    
    public function profileAction() {
    	$user = $this->userService->getUser($this->_getParam('id'));
    	
    	$this->view->user = $user;
    	$this->view->currentUser = $this->userService->getCurrentUser();
    	$this->view->workoutService = $this->workoutService;
    }

    public function showGoalAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	$this->view->user = $this->userService->getCurrentUser();
    }
    
	public function showGoalFormAction() {
		$this->_helper->disableLayout();
		
	}
	
	public function setGoalAction() {
		$this->_helper->disableView();
		
		if ($this->_request->isPost()) {
			$user = $this->userService->getCurrentUser();
			$this->userService->setGoal($user, $this->_getParam('goal'), $this->_getParam('value'), $this->_getParam('unit'));
		}
	}
    
	public function addFriendAction() {
		$this->_helper->disableView();
		$this->userService->addFollowing($this->_getParam('id'));
	}
	
	public function removeFriendAction() {
		$this->_helper->disableView();
		$this->userService->removeFollowing($this->_getParam('id'));
	}
}
	
