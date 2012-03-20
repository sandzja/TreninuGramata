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
	
	public function showSearchFormAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableLayout();
		}
		 
		$this->view->name = $this->_getParam('name');
	}
	
	public function inviteAction() {
		$twitterSession = new \Zend_session_Namespace('twitter');
		
		$this->view->type = $this->_getParam('type', $twitterSession->oauthToken == null ? 'facebook' : 'twitter');
		$this->view->page = $this->_getParam('page');
		$this->view->currentUser = $this->userService->getCurrentUser();
	}
	
	public function inviteExternalFriendsPostsAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableLayout();
		}
		
		if ($this->_getParam('type') == 'twitter') {
			$friends = $this->userService->getTwitterFriends($this->userService->getCurrentUser())->slice($this->_getParam('page') * 10, 10);
		} else {
			$friends = $this->userService->getFacebookFriends($this->userService->getCurrentUser())->slice($this->_getParam('page') * 10, 10);
		}
		
		$this->view->users = $friends;
		$this->view->type = $this->_getParam('type');
	}
	
	public function sendFacebookInvitationAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
		
		$this->userService->inviteFacebook($this->userService->getCurrentUser(), $this->_getParam('id'), 'I\'m using Trainingbook.com application on my phone. Please check it out at http://trainingbook.com.');
	}
	
	public function sendTwitterInvitationAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
	
		$this->userService->inviteTwitter($this->userService->getCurrentUser(), $this->_getParam('id'), 'I\'m using Trainingbook.com application on my phone. Please check it out at http://trainingbook.com.');
	}
	
	public function sendFacebookShareAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
	
		$this->userService->inviteFacebook($this->userService->getCurrentUser(), $this->_getParam('id'), 'I highly recommend workout plan! Try it yourself! Please check it out at http://trainingbook.com.');
	}
	
	public function sendTwitterShareAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
	
		$this->userService->inviteTwitter($this->userService->getCurrentUser(), $this->_getParam('id'), 'I highly recommend workout plan! Try it yourself! Please check it out at http://trainingbook.com.');
	}
	
	public function sendFacebookRecommendAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
	
		$this->userService->inviteFacebook($this->userService->getCurrentUser(), $this->_getParam('id'), 'I liked a workout a lot! Can you try this yourself as well on Trainingbook. Please check it out at http://trainingbook.com.');
	}
	
	public function sendTwitterRecommendAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
	
		$this->userService->inviteTwitter($this->userService->getCurrentUser(), $this->_getParam('id'), 'I liked a workout a lot! Can you try this yourself as well on Trainingbook.. Please check it out at http://trainingbook.com.');
	}
	
	public function sendEmailInvitationAction() {
		foreach ($this->_getParam('emails') as $email) {
			$this->userService->invviteEmail($this->userService->getCurrentUser(), $email);
		}
		
		$this->_helper->redirector('invite');
	}
 }