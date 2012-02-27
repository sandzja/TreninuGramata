<?php
namespace Service\Api;

use Service\ServiceManager;

use Service\Api\AbstractService;

class User {
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function __construct() {
		$this->userService = \Service\ServiceManager::factory(new \Service\User());
	}
	
	public function getUserData($UserID, $SessionID, $UserDataRequest) {
		$this->userService = new \Service\User();
		
		$user = $this->checkAndUpdateSession($UserID, $SessionID);

		if ($user->getUpdatedTime()->getTimestamp() < $UserDataRequest['UserUpdated']) {
			throw new \Zend_Exception('User data is older on the server', 3);
		}
		
		$birthDateTimestamp = null;
		$birthDate = $user->getBirtDate();
		if ($birthDate != null) {
			$birthDateTimestamp = $birthDate->getTimestamp();
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'UserData' => array (
				'UserEMail' => $user->getEmail(),
				'UserName' => $user->getName(),
				'UserBirthDate' => $birthDateTimestamp,
				'UserHeight' => $user->getHeight(),
				'UserWeight' => $user->getWeight(),
				'UserUpdated' => $user->getUpdatedTime()->getTimestamp(),
			),
		));
		
		return $response;
	}
	
	public function update($UserID, $SessionID, $UserData) {
		$user = $this->checkAndUpdateSession($UserID, $SessionID);

		if ($user->getUpdatedTime()->getTimestamp() > @$UserData['UserUpdated']) {
			throw new \Zend_Exception('User data is newer on the server', 4);
		}
		
		$user->setEmail(@$UserData['UserEMail']);
		$user->setName(@$UserData['UserName']);
		$user->setBirthDate(new \DateTime(@$UserData['UserBirthDate']));
		$user->setHeight(@$UserData['UserHeight']);
		$user->setWeight(@$UserData['UserWeight']);
		$user->setUpdatedTime(new \DateTime(@$UserData['UserUpdated']));
		
		$this->userService->persistUser($user);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'UserUpdated' => $user->getUpdatedTime()->getTimestamp(),
		));
		
		return $response;
	}
	
	/**
	 * @param int $UserID
	 * @param string $SessionID
	 * @throws \Zend_Exception
	 * @return \Entity\User
	 */
	public function checkAndUpdateSession($UserID, $SessionID) {
		$user = $this->userService->getUser($UserID);
		
		if ($user == null) {
			throw new \Zend_Exception('User not found', 0);
		}
		
		if ($user->getSessionValidTime() <= new \DateTime('now') || $user->getSessionId() != $SessionID) {
			throw new \Zend_Exception('Session is expired', 5);
		}
		
		$user->setSessionValidTime(new \DateTime('+15 min'));
		$this->userService->persistUser($user);
		
		return $user;
	}
	
}