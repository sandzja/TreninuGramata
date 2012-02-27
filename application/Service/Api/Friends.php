<?php
namespace Service\Api;

use Service\ServiceManager;

use Service\Api\AbstractService;

class Friends {
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	/**
	 * @var \Service\NewsFeed
	 */
	private $newsFeedService;
	
	public function __construct() {
		$this->userService = \Service\ServiceManager::factory(new \Service\User());
		$this->newsFeedService = \Service\ServiceManager::factory(new \Service\NewsFeed());
	}
	
	public function getFeedPosts($userId, $sessionId, $limitNr) {
		$userService = new \Service\Api\User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);

		$posts = $this->newsFeedService->getUserPosts($user->getId(), null, $limitNr, null, array (
			'\Entity\Feed\Post\Note',
			'\Entity\Feed\Post\Workout',
		));
		
		$feedPosts = array ();

		foreach ($posts as $post) /* @var $post \Entity\Feed\Post */ {
			$workoutName = null;
			$workoutRating = null;
			if ($post instanceof \Entity\Feed\Post\Workout) {
				$workoutName = $post->getWorkout()->getName();
				$workoutRating = $post->getWorkout()->getRating();
			}
			
			$feedPosts[] = array (
				'FeedID' => $post->getId(),
				'FeedType' => get_class($post),
				'FeedDate' => $post->getDateAdded()->getTimestamp(),
				'UserName' => $post->getAuthor()->getName(),
				'WorkoutName' => $workoutName,
				'FeedNote' => $post->getComment(),
				'FeedCommentsNr' => count($post->getComments()),
				'WorkoutRating' => $workoutRating,
			);
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'FeedPosts' => $feedPosts,
		));
		
		return $response;
	}
	
	public function friendsList($UserID, $SessionID) {
		$userService = new \Service\Api\User();
	
		$user = $userService->checkAndUpdateSession($UserID, $SessionID);
	
		$data = array ();
		foreach ($user->getFollowings() as $friend) /* @var $friend \Entity\User */ {
			$data[] = array (
					'FriendUserId' => $friend->getId(),
					'FriendName' => $friend->getName(),
					'FriendImage' => $friend->getProfileImageUrl(),
					'FriendWorkoutsNr' => count($friend->getWorkouts()),
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Friends' => $data,
		));
	
		return $response;
	}
}