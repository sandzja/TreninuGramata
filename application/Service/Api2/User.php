<?php
namespace Service\Api2;

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
	
	public function getUserData($UserID, $SessionID, $UserDataRequest, $targetUserId = null) {
		$user = $this->checkAndUpdateSession($UserID, $SessionID);

		if ($user->getUpdatedTime()->getTimestamp() < $UserDataRequest['UserUpdated']) {
			throw new \Zend_Exception('User data is older on the server', 3);
		}
		
		if ($targetUserId != null) {
		    $targetUser = $this->userService->getUser($targetUserId);
		    if ($targetUser != null) {
		        $user = $targetUser;
		    }
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
				'UserProfilePicture' => $user->getProfileImageUrl(),
				'UserWorkoutsNumber' => $user->getWorkouts()->count(),
				'UserDuration' => $user->countTime(),
				'UserDistance' => $user->countDistances(),
			),
		));
		
		return $response;
	}
	
	public function setUserData($UserID, $SessionID, $UserData) {
		$user = $this->checkAndUpdateSession($UserID, $SessionID);

		if ($user->getUpdatedTime()->getTimestamp() > @$UserData['userUpdated']) {
			throw new \Zend_Exception('User data is newer on the server', 4);
		}
		
		$user->setEmail(@$UserData['userEMail']);
		$user->setName(@$UserData['userName']);
		$user->setBirthDate(new \DateTime('@' . (int) $UserData['userAge']));
		$user->setHeight(@$UserData['userHeight']);
		$user->setWeight(@$UserData['userWeight']);
		$user->setUpdatedTime(new \DateTime('@' . (int) $UserData['userUpdated']));
		
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
			throw new \Zend_Exception('User not found', 9);
		}
		
		if ($user->getSessionValidTime() <= new \DateTime('now') || $user->getSessionId() != $SessionID) {
			throw new \Zend_Exception('Session is expired', 8);
		}
		
		$user->setSessionValidTime(new \DateTime('+15 min'));
		$this->userService->persistUser($user);
		
		return $user;
	}
}