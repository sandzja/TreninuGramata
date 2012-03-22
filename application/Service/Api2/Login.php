<?php
namespace Service\Api2;

use Service\ServiceManager;
use Entity\User;

class Login {
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function __construct() {
		$this->userService = ServiceManager::factory(new \Service\User());
	}
	
	public function facebook($FacebookUID, $FacebookAccessToken) {
		try {
			$user = $this->userService->createFacebookUser($FacebookUID, $FacebookAccessToken);
		} catch (\Facebook_GraphApiException $e) {
			throw new \Zend_Exception('Access token is invalid', 0, $e);
		}
		
 		$user->setFacebookAccessToken($FacebookAccessToken);
 		$this->userService->getEntityManager()->persist($user);
		
		$this->userService->updateSessionTime($user);
		
		$this->userService->updateFacebookData($user->getId());
		
		
		return $this->response($user);
	}
	
	public function twitter($TwitterUID, $TwitterOAuthToken, $TwitterOAuthTokenSecret) {
		
		$user = $this->userService->createTwitterUser($TwitterUID, $TwitterOAuthToken, $TwitterOAuthTokenSecret);
		$user->setTwitterOAuthToken($TwitterOAuthToken);
		$user->setTwitterOAuthTokenSecret($TwitterOAuthTokenSecret);
		
		$this->userService->updateSessionTime($user);
		
		return $this->response($user);
	}
	
	public function authorizeFacebook($userId, $sessionId, $facebookUID, $facebookAccessToken) {
	    $userService = new \Service\Api2\User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    $user->setFacebookUserId($facebookUID);
	    $user->setFacebookAccessToken($facebookAccessToken);
	    $this->userService->persistUser($user);
	    
	    return \Zend_Json::encode(array (
	    		'Response' => 'OK',
	    ));
	}
	
	public function authorizeTwitter($userId, $sessionId, $twitterUID, $twitterOAuthToken, $twitterOAuthTokenSecret) {
		$userService = new \Service\Api2\User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		 
		$user->setTwitterUserId($twitterUID);
		$this->userService->persistUser($user);
		 
		return \Zend_Json::encode(array (
				'Response' => 'OK',
		));
	}
	
	private function response(\Entity\User $user) {
		return \Zend_Json::encode(array (
			'Response' => 'OK',
			'SessionID' => $user->getSessionId(),
			'UserID' => $user->getId(),
		));
	}
	
}