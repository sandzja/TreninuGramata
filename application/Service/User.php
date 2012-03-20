<?php
namespace Service;

use Doctrine\Common\Collections\ArrayCollection;

class User extends AbstractService {
	
	/**
	 * @param int $facebookUserId
	 * @return \Entity\User
	 */
	public function getUserByFacebook($facebookUserId) {
		$user = $this->em->getRepository('Entity\User')->findOneByFacebookUserId($facebookUserId);
		
		return $user;
	}
	
	public function getUserByTwitter($twitterUserId) {
		$user = $this->em->getRepository('Entity\User')->findOneByTwitterUserId($twitterUserId);
		
		return $user;
	}
	
	/**
	 * Return user object
	 * @param int $id
	 * @return \Entity\User
	 */
	public function getUser($id) {
		$user = $this->em->find('Entity\User', $id);

		return $user;
	}
	
	/**
	 * Return logged in user object
	 * @return Entity\User
	 */
	public function getCurrentUser() {
		if (!\Zend_Auth::getInstance()->hasIdentity()) {
			return null;
		}
		
		return $this->getUser(\Zend_Auth::getInstance()->getIdentity()->getId());
	}
	
	/**
	 * Return user id
	 * @param \Entity\User $user
	 * @return \Entity\User
	 */
	public function persistUser(\Entity\User $user) {
		$this->em->persist($user);
		
		return $user;
	}
	
	public function updateSessionTime(\Entity\User $user) {
		$user->setSessionValidTime(new \DateTime('now + 15 min'));
		
		$this->persistUser($user);
	}
	
	/**
	 * Saves User to session
	 * @param \Entity\User $user
	 */
	public function saveUserToSession(\Entity\User $user) {
		$auth = \Zend_Auth::getInstance();
		$auth->getStorage()->write($user);
	}
	
	public function logout(){
		$auth = \Zend_Auth::getInstance();
		$auth->clearIdentity();
		\Zend_Session::destroy();
		$fbKey = 'fbs_' . $this->config->facebook->appId;
		setcookie($fbKey, '', time()-3600, '/', '.' . $_SERVER['HTTP_HOST']);
	}
	
	public function getFacebookLoginUrl() {
		$facebookApi = new \Facebook_Graph($this->config->facebook->toArray());

		return $facebookApi->getLoginUrl($this->config->facebook->loginParams->toArray());
	}
	
	public function getTwitterLoginUrl() {
		require_once 'Twitter/EpiCurl.php';
		require_once 'Twitter/EpiOAuth.php';
		require_once 'Twitter/EpiTwitter.php';
			
		$config = $this->config->twitter;
		
		$twitter = new \EpiTwitter($config->consumerKey, $config->consumerSecret);
		return $twitter->getAuthenticateUrl();
	}
	
	public function loginFacebook() {
		$facebookApi = new \Facebook_Graph($this->config->facebook->toArray());
		if ($facebookApi->getUser() == null) {
			return false;
		}

		$facebookSession = $facebookApi->getSession();
		$user = $this->createFacebookUser($facebookApi->getUser(), $facebookSession['access_token']);
		$user->setFacebookAccessToken($facebookSession['access_token']);
		$this->saveUserToSession($user);
		
		$this->updateFacebookData();
		
		return true;
	}
	
	public function loginTwitter() {
		require_once 'Twitter/EpiCurl.php';
		require_once 'Twitter/EpiOAuth.php';
		require_once 'Twitter/EpiTwitter.php';
		
		$config = $this->config->twitter;
		
		$twitterSession = new \Zend_session_Namespace('twitter');
		$twitterSession->oauthToken = @$_GET['oauth_token'];

		if ($twitterSession->oauthToken == null) {
			return false;
		}
		
		$twitter = new \EpiTwitter($config->consumerKey, $config->consumerSecret);
		$twitter->setToken($twitterSession->oauthToken);
		$token = $twitter->getAccessToken();
		$twitter->setToken($token->oauth_token, $token->oauth_token_secret);
		$twitterSession->oauthToken = $token->oauth_token;
		$twitterSession->oauthSecret = $token->oauth_token_secret;
		
		$userInfo = $twitter->get('/account/verify_credentials.json');

		$user = $this->createTwitterUser($userInfo->response['id'], $twitter->getAccessToken(), null);
		$user->setTwitterOAuthToken($token->oauth_token);
		$user->setTwitterOAuthTokenSecret($token->oauth_token_secret);
		$this->saveUserToSession($user);
		
		$this->syncTwitterFriends();
		
		return true;
	}
	
	public function createFacebookUser($facebookUserId, $facebookAccessToken) {
		$user = $this->getUserByFacebook($facebookUserId);

		if ($user == null) {
			$facebookApi = new \Facebook_Graph($this->config->facebook->toArray());
			try {
				$facebookUser = $facebookApi->api('/me', array (
					'access_token' => $facebookAccessToken,
				));
			} catch (\Facebook_GraphApiException $e) {
				throw new $e;
			}
			$user = new \Entity\User();
			$user->setFacebookUserId(@$facebookUser['id']);
			$user->setEmail(@$facebookUser['email']);
			$user->setName(@$facebookUser['name']);
			$user->setBirthDate(@$facebookUser['birthday'] != '' ? new \DateTime($facebookUser['birthday']) : null);
			$user->setUpdatedTime(new \DateTime());
			$user->setProfileImageUrl('https://graph.facebook.com/' . $facebookUser['id'] . '/picture?type=large');
			$user->setFacebookAccessToken($facebookAccessToken);
			$this->em->persist($user);
			$this->refreshTransaction();
			
			$workoutService = new \Service\Workout();
			$workoutService->createDefaultTrainingData($user);
			$this->refreshTransaction();
			
			$newsFeedService = new \Service\NewsFeed();
			$newsFeedService->postFacebook(null, 'I just created my personal workout\'s Trainingbook. Now I have a training assistant who will add fun and effectiveness to my workouts and let me track and share my progress.', $this->config->meta->domainName, 'TrainingBook', null, $user);
		}
		
		return $user;
	}
	
	public function createTwitterUser($twitterUID, $twitterOAuthToken, $twitterOAuthTokenSecret) {
		$user = $this->getUserByTwitter($twitterUID);
		
		if ($user == null) {
			require_once 'Twitter/twitteroauth.php';
			
			$config = $this->config->twitter;
			
			$twitteroauth = new \TwitterOAuth($config->consumerKey, $config->consumerSecret, $twitterOAuthToken, $twitterOAuthTokenSecret);
			$userInfo = $twitteroauth->get('users/show', array (
				'user_id' => $twitterUID,
			));
			
			if (isset($userInfo->error) || $userInfo == null) {
				throw new \Zend_Exception('Access token is invalid', 0);
			}
			
			$user = new \Entity\User();
			$user->setTwitterUserId($userInfo->id);
			$user->setName($userInfo->name);
			$user->setUpdatedTime(new \DateTime());
			$user->setProfileImageUrl('https://api.twitter.com/1/users/profile_image?screen_name=' . $userInfo->screen_name . '&size=original');
			
			$this->em->persist($user);
			
			$newsFeedService = new \Service\NewsFeed();
			$newsFeedService->postTwitter('I just created my personal workout\'s Trainingbook. Now I have a training assistant who will add fun and effectiveness to my workouts and let me track and share my progress.', $user);
			
			$workoutService = new \Service\Workout();
			$workoutService->createDefaultTrainingData($user);
		}

		return $user;
	}
	
	public function updateFacebookData($userId = null) {
		if ($userId == null) {
			$user = $this->getCurrentUser();
		} else {
			$user = $this->getUser($userId);
		}
		
		if ($user == null) {
			throw new \Zend_Exception('Facebook user id was not found');
		}
		
		if ($user->getFacebookUserId() == null) {
			return null;
		}
		
		$facebook = new \Facebook_Graph($this->config->facebook->toArray());
		
		$params = array ();
		$params['access_token'] = $user->getFacebookAccessToken();
		try {
			$facebookUser = $facebook->api('/me', 'GET', $params);
		} catch (\Facebook_GraphApiException $e) {
			return null;
		}
		$user->setEmail($facebookUser['email']);
		$user->setName($facebookUser['name']);
		$user->setBirthDate(@$facebookUser['birthday'] != '' ? new \DateTime($facebookUser['birthday']) : null);
		$user->setUpdatedTime(new \DateTime());
		$user->setProfileImageUrl('https://graph.facebook.com/' . $facebookUser['id'] . '/picture?type=large');
		
		$friendsData = (object) $facebook->api('/' . $user->getFacebookUserId() . '/friends', 'GET', $params);
		$friends = $friendsData->data;
		foreach ($friends as $facebookFriend) {
			$facebookFriend = (object) $facebookFriend;
			$friend = $this->getUserByFacebook($facebookFriend->id);
			
			if ($friend != null) {
				if ($friend != null) {
					$this->addFollowing($friend->getId(), $user);
				}
				
				if (!$friend->getFollowings()->contains($user)) {
					$friend->addFollowing($user);
				}
				
				$this->em->persist($friend);
			}
		}
	}
	
	public function syncTwitterFriends() {
		require_once 'Twitter/EpiCurl.php';
		require_once 'Twitter/EpiOAuth.php';
		require_once 'Twitter/EpiTwitter.php';
		
		$user = $this->getCurrentUser();
		if ($user == null) {
			return null;
		}
		
		if ($user->getTwitterUserId() == null) {
			return null;
		}
		
		$config = $this->config->twitter;
		$twitterSession = new \Zend_session_Namespace('twitter');

		$twitter = new \EpiTwitter($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
		
		$userInfo = $twitter->get('/followers/ids.json', array (
			'user_id' => $user->getTwitterUserId(),
		));

		$friendIds = $userInfo->response;
		
		foreach ($friendIds as $friendId) {
			$friend = $this->getUserByTwitter($friendId);
			if ($friend != null) {
				$this->addFollowing($friend->getId());
			}
		}
	}
	
	public function addFollowing($friendId, \Entity\User $user = null) {
		if ($user == null) {
	    	$user = $this->getCurrentUser();
		}
		
		$friend = $this->getUser($friendId);
		if ($friend != null) {
			if (!$user->getFollowings()->contains($friend)) {
				$user->addFollowing($friend);
				$this->em->persist($user);
			}
		}
	}
	
	public function removeFollowing($friendId) {
	    $user = $this->getCurrentUser();
	    $friend = $this->getUser($friendId);
	    if ($friend != null) {
	        if ($user->getFollowings()->contains($friend)) {
	            $user->getFollowings()->removeElement($friend);
	        }
	    }
	}
	
	public function getOverallTimeGraph() {
		$user = $this->getCurrentUser();
		
		$times = $this->em->getRepository('Entity\Workout')->getOverallTimeGraph($user);
		$maxTime = 0.1;
		foreach ($times as $time) {
			if ($maxTime < $time['seconds']) {
				$maxTime = $time['seconds'];
			}
		}
		
		foreach ($times as &$time) {
			$time['percent'] = round($time['seconds'] / $maxTime * 100);
		}

		return $times;
	}
	
	public function getDistancesGraph() {
		$user = $this->getCurrentUser();
	
		$distances = $this->em->getRepository('Entity\Workout')->getDistanceGraph($user);
		$max = 0;
		foreach ($distances as $distance) {
			if ($max < $distance['distance']) {
				$max = $distance['distance'];
			}
		}
	
		foreach ($distances as &$distance) {
			$distance['percent'] = $max == 0 ? 0 : round($distance['distance'] / $max * 100);
		}
	
		return $distances;
	}
	
	public function getWorkoutsGraph() {
		$user = $this->getCurrentUser();
	
		$workouts = $this->em->getRepository('Entity\Workout')->getWorkoutGraph($user);
		$max = 0;
		foreach ($workouts as $workout) {
			if ($max < $workout['workout']) {
				$max = $workout['workout'];
			}
		}
	
		foreach ($workouts as &$workout) {
			$workout['percent'] = round($workout['workout'] / $max * 100);
		}
	
		return $workouts;
	}
	
	public function searchFollowers($name, $limit = null, $offset = null) {
		$user = $this->getCurrentUser();
		
		$friends = $this->em->getRepository('Entity\User')->searchFollowers($user, $name, $limit, $offset);
		
		return $friends;
	}
	
	public function searchFollowing($name, $limit = null, $offset = null) {
		$user = $this->getCurrentUser();
	
		$friends = $this->em->getRepository('Entity\User')->searchFollowing($user, $name, $limit, $offset);
	
		return $friends;
	}
	
	public function searchUsers($name, $limit = null, $offset = null) {
		$users = $this->em->getRepository('Entity\User')->searchUsers($name, $limit, $offset);
	
		return $users;
	}
	
	public function getAllUsers($limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\User')->findBy(array (), array ('name' => 'asc'), $limit, $offset);
	}
	
	public function setDistanceGoal(\Entity\User $user, $goal, $value, $unit) {
		$user->setGoal($value * (int) $unit);
		$user->setGoalType('distance');
		
		$this->em->persist($user);
	}
	
	public function setTimeGoal(\Entity\User $user, $goal, $hours, $minutes, $seconds) {
		$user->setGoal(((int) $hours * 60 * 60) + ((int) $minutes * 60) + ((int) $seconds));
		$user->setGoalType('time');
	
		$this->em->persist($user);
	}
	
	public function setCaloriesGoal(\Entity\User $user, $goal, $value) {
		$user->setGoal($value);
		$user->setGoalType('calories');
		
		$this->em->persist($user);
	}
	
	public function setWorkoutGoal(\Entity\User $user, $goal, $value) {
		$user->setGoal($value);
		$user->setGoalType('workout');
	
		$this->em->persist($user);
	}
	
	public function inviteFacebook(\Entity\User $user, $facbookFriendId, $message) {
		$facebook = new \Facebook_Graph($this->config->facebook->toArray());
		
		$params = array (
				'message' => stripslashes(htmlspecialchars($message)),
		);
		
		if ($user != null) {
			$params['access_token'] = $user->getFacebookAccessToken();
		}
		
		$params['link'] = 'http://trainingbook.com';
		$params['name'] = 'TrainingBook';
		
		try {
			$result = $facebook->api($facbookFriendId . '/feed', 'POST', $params);
		} catch (\Facebook_GraphApiException $e) {
			return null;
		}
		
	}
	
	public function inviteTwitter(\Entity\User $user, $facbookFriendId, $message) {
	}
	
	public function invviteEmail(\Entity\User $user, $email) {
		$mail = new \Zend_Mail();
		$mail->setFrom($user->getEmail(), $user->getName());
		$mail->addTo($email);
		$mail->setSubject($user->getName() . ' has invited you to use Trainingbook application');
		$mail->setBodyText('
Hi ' . $email . '!
	
I\'m using Trainingbook.com application on my phone. Please check it out at http://trainingbook.com. 

Trainingbook purpose is to motivate people being active and healthy by bringing fun and sense to their workouts 

Trainingbook.com is every person\'s personal training partner – smart (tracks training progress, provides sports and nutrition advices), 
communicable (allows to share and enrich the training experience) and fun (motivates to go out and have the best emotion out of every move).

Trainingbook.com is my virtual motivator in living active and healthy life!');
		$mail->send();
	}
	
	public function getUnsignedFacebookFriends(\Entity\User $user) {
		$facebook = new \Facebook_Graph($this->config->facebook->toArray());
		$params = array ();
		$params['access_token'] = $user->getFacebookAccessToken();
		
		$friendsData = (object) $facebook->api('/' . $user->getFacebookUserId() . '/friends', 'GET', $params);
		$friends = $friendsData->data;
		
		$unsignedFriends = array ();
		foreach ($friends as $facebookFriend) {
			$facebookFriend = (object) $facebookFriend;
			$friend = $this->getUserByFacebook($facebookFriend->id);
				
			if ($friend == null) {
				$unsignedFriends[] = array (
					'id' => $facebookFriend->id,
					'name' => $facebookFriend->name,
					'profileImage' => 'https://graph.facebook.com/' . $facebookFriend->id . '/picture?type=large'
				);
			}
		}
		
		return new ArrayCollection($unsignedFriends);
	}
	
	public function getFacebookFriends(\Entity\User $user) {
		$facebook = new \Facebook_Graph($this->config->facebook->toArray());
		$params = array ();
		$params['access_token'] = $user->getFacebookAccessToken();
	
		$friendsData = (object) $facebook->api('/' . $user->getFacebookUserId() . '/friends', 'GET', $params);
		$friends = $friendsData->data;
	
		$unsignedFriends = array ();
		foreach ($friends as $facebookFriend) {
			$facebookFriend = (object) $facebookFriend;
	
			$unsignedFriends[] = array (
					'id' => $facebookFriend->id,
					'name' => $facebookFriend->name,
					'profileImage' => 'https://graph.facebook.com/' . $facebookFriend->id . '/picture?type=large'
			);
		}
	
		return new ArrayCollection($unsignedFriends);
	}
	
	public function getTwitterFriends(\Entity\User $user) {
		require_once 'Twitter/EpiCurl.php';
		require_once 'Twitter/EpiOAuth.php';
		require_once 'Twitter/EpiTwitter.php';
		require_once 'Twitter/twitteroauth.php';
	
		$user = $this->getCurrentUser();
		if ($user == null) {
			return null;
		}
	
		if ($user->getTwitterUserId() == null) {
			return null;
		}
	
		$config = $this->config->twitter;
		$twitterSession = new \Zend_session_Namespace('twitter');
	
		$twitter = new \EpiTwitter($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
	
		$userInfo = $twitter->get('/followers/ids.json', array (
				'user_id' => $user->getTwitterUserId(),
		));
	
		$friendIds = $userInfo->response['ids'];
	
		$twitteroauth = new \TwitterOAuth($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
		$unsignedFriends = array ();
		foreach ($friendIds as $friendId) {
			$userInfo = $twitteroauth->get('users/show', array (
					'user_id' => $friendId,
			));
				
			if (isset($userInfo->error) || $userInfo == null) {
				throw new \Zend_Exception('Access token is invalid', 0);
			}
				
			$unsignedFriends[] = array (
					'id' => $userInfo->id,
					'name' => $userInfo->name,
					'profileImage' => $userInfo->profile_image_url
			);
		}
	
		return new ArrayCollection($unsignedFriends);
	}
	
	public function getUnsignedTwitterFriends(\Entity\User $user) {
		require_once 'Twitter/EpiCurl.php';
		require_once 'Twitter/EpiOAuth.php';
		require_once 'Twitter/EpiTwitter.php';
		require_once 'Twitter/twitteroauth.php';
		
		$user = $this->getCurrentUser();
		if ($user == null) {
			return null;
		}
	
		if ($user->getTwitterUserId() == null) {
			return null;
		}
	
		$config = $this->config->twitter;
		$twitterSession = new \Zend_session_Namespace('twitter');
	
		$twitter = new \EpiTwitter($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
	
		$userInfo = $twitter->get('/followers/ids.json', array (
				'user_id' => $user->getTwitterUserId(),
		));
	
		$friendIds = $userInfo->response['ids'];
	
		$twitteroauth = new \TwitterOAuth($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
		$unsignedFriends = array ();
		foreach ($friendIds as $friendId) {
			$userInfo = $twitteroauth->get('users/show', array (
				'user_id' => $friendId,
			));
			
			if (isset($userInfo->error) || $userInfo == null) {
				throw new \Zend_Exception('Access token is invalid', 0);
			}
			
			$friend = $this->getUserByTwitter($friendId);
			if ($friend == null) {
				$unsignedFriends[] = array (
						'id' => $userInfo->id,
						'name' => $userInfo->name,
						'profileImage' => $userInfo->profile_image_url
				);
			}
		}

		return new ArrayCollection($unsignedFriends);
	}
	
}