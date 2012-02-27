<?php
namespace Service\Api;

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
		
		$this->userService->updateSessionTime($user);

		return $this->response($user);
	}
	
	public function twitter($TwitterUID, $TwitterOAuthToken, $TwitterOAuthTokenSecret) {
		
		$user = $this->userService->createTwitterUser($TwitterUID, $TwitterOAuthToken, $TwitterOAuthTokenSecret);
		$this->userService->updateSessionTime($user);
		
		return $this->response($user);
	}
	
	private function response(\Entity\User $user) {
		return \Zend_Json::encode(array (
			'Response' => 'OK',
			'SessionID' => $user->getSessionId(),
			'UserID' => $user->getId(),
		));
	}
	
}