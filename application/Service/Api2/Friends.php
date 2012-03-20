<?php
namespace Service\Api2;

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
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);

		$posts = $this->newsFeedService->getUserPosts($user->getId(), null, $limitNr, null, array (
			'\Entity\Feed\Post\Note',
			'\Entity\Feed\Post\Workout',
		));
		
		$feedPosts = array ();
		foreach ($posts as $post) /* @var $post \Entity\Feed\Post */ {
			$workoutName = null;
			$workoutRating = null;
			
			$trainingPlanreports = array ();
			if ($post instanceof \Entity\Feed\Post\Workout) {
				$workoutName = $post->getWorkout()->getName();
				$workoutRating = $post->getWorkout()->getRating();
				
				foreach ($post->getWorkout()->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
				    $trainingPlanreports[] = array (
					    'TrainingPlanSportName' => $trainingPlanReport->getTrainingPlan()->getSport()->getName(),
					    'ReportDistance' => $trainingPlanReport->getDistance(),
					    'TrainingPlanReportID' => $trainingPlanReport->getId(),
				    );
				}
			}
			
			$feedPosts[] = array (
				'FeedID' => $post->getId(),
				'FeedType' => get_class($post),
				'FeedDate' => $post->getDateAdded()->getTimestamp(),
				'UserName' => $post->getAuthor()->getName(),
				'UserID' => $post->getAuthor()->getId(),
				'TrainingPlanReports' => $trainingPlanreports,
				'FeedNote' => $post->getComment(),
				'FeedCommentsNr' => count($post->getComments()),
				'WorkoutRating' => $workoutRating,
				'UserImage' => $post->getAuthor()->getProfileImageUrl(),
			);
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'FeedPosts' => $feedPosts,
		));
		
		return $response;
	}
	
	public function getChallengePosts($userId, $sessionId, $limitNr) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);

		$posts = $this->newsFeedService->getUserPosts($user->getId(), null, $limitNr, null, array (
			'\Entity\Feed\Post\Challenge',
		));
		
		$feedPosts = array ();
		foreach ($posts as $feedPost) /* @var $feedPost \Entity\Feed\Post\Challenge */ {
		    
			$challengeEntity = null;
			if ($feedPost->getChallenge()->getWorkout() != null) {
				$challengeEntity = $feedPost->getChallenge()->getWorkout();
			} else if ($feedPost->getChallenge()->getRecord() != null) {
				$challengeEntity = $feedPost->getChallenge()->getRecord();
			}
			
			$exercises = array ();
			foreach ($feedPost->getChallenge()->getTrainingPlan()->getExercises() as $exercise) /* @var $exercise \Entity\Exercise */ {
				$exercises[] = array (
					'ExerciseGoalDuration' => $exercise->getGoal()->getDuration(),
					'ExerciseGoalDistance' => $exercise->getGoal()->getDistance(),
				);
			}
			
			$feedPosts[] = array (
				'FeedID' => $feedPost->getId(),
				'FeedDate' => $feedPost->getDateAdded()->getTimestamp(),
				'UserName' => $feedPost->getAuthor()->getName(),
				'OpponentName' => $feedPost->getChallenge()->getOpponentUser()->getName(),
				'FeedCommentsNr' => count($feedPost->getComments()),
				'TrainingPlanSportName' => $feedPost->getChallenge()->getTrainingPlan()->getSport()->getName(),
				'Exercises' => $exercises,
				'ChallengedDuration' => $challengeEntity != null ? $challengeEntity->getDuration() : null,
				'ChallengedDistance' => $challengeEntity != null ? $challengeEntity->getDistance() : null,
				'ChallengerWon' => $feedPost->getChallenge()->getChallengeReport() == null ? false : ((boolean) $feedPost->getChallenge()->getChallengeReport()->didWinChallenge()),
				'ChallengeResult' => $feedPost->getComment(),
				'PostNote' => $feedPost->getComment(),
				'UserImage' => $feedPost->getAuthor()->getProfileImageUrl(),
			);
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'FeedPosts' => $feedPosts,
		));
		
		return $response;
	}
	
	public function getChallengeDetails($userId, $sessionId, $feedId) {
	    $userService = new User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    /* @var $post \Entity\Feed\Post\Challenge */
	    $feedPost = $this->newsFeedService->getPost($feedId);
	    if (!($feedPost instanceof \Entity\Feed\Post\Challenge)) {
	    	throw new \Zend_Exception('Feed post is not Challenge post', 12);
	    }
	    
	    $feedComments = array ();
	    foreach ($feedPost->getComments() as $comment) /* @var $comment \Entity\Feed\Comment */{
	    	$feedComments[] = array (
	    			'CommentText' => $comment->getText(),
	    			'CommentDate' => $comment->getDateAdded()->getTimestamp(),
	    			'CommentUserName' => $comment->getAuthor()->getName(),
	    	);
	    }
	    
	    $trainingPlanReports = array ();
    	$trainingPlanReports[] = array (
    			'TrainingPlanName' => $feedPost->getChallenge()->getTrainingPlan()->getName(),
    			'ReportDistance' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getDistance(),
    			'ReportDuration' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getDuration(),
    			'BurnedCalories' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getBurnedCalories(),
    			'ReportID' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getId(),
    			'ReportAverageSpeed' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getAverageSpeed(),
    			'ReportAverageHeartRate' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getAverageHeartRate(),
    			'WorkoutPlaylist' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getWorkout()->getPlayList(),
    	);
	    
	    $response = \Zend_Json::encode(array (
	    		'Response' => 'OK',
	    		'ChallengePostDetails' => array (
    				'WorkoutID' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getWorkout()->getId(),
    				'WorkoutPlaylist' => $feedPost->getChallenge()->getChallengeReport()->getTrainingPlanReport()->getWorkout()->getPlayList(),
    				'UserWorkoutsNr' => $feedPost->getAuthor()->getWorkouts()->count(),
    				'UserDuration' => $feedPost->getAuthor()->countTime(),
    				'UserDistance' => $feedPost->getAuthor()->countDistances(),
    				'FeedComments' => $feedComments,
    				'TrainingPlanReports' => $trainingPlanReports,
	    		),
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
				'FriendDuration' => $friend->countTime(),
				'FriendDistance' => $friend->countDistances()
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Friends' => $data,
		));
	
		return $response;
	}

	public function sendFeedNote($userId, $sessionId, $feedNote) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$params = new \Service\DTO\RequestParams();
		$params->userId = $userId;
		$params->note = $feedNote;
		
		$this->newsFeedService->saveNote($params);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function sendFeedComment($userId, $sessionId, $feedId, $comment) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$this->newsFeedService->addComment($feedId, $comment, $userId);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}

	public function getWorkoutDetails($userId, $sessionId, $feedId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		/* @var $post \Entity\Feed\Post\Workout */
		$post = $this->newsFeedService->getPost($feedId);
		if (!($post instanceof \Entity\Feed\Post\Workout)) {
			throw new \Zend_Exception('Feed post is not Workout post', 12);
		}
		
		$feedComments = array ();
		foreach ($post->getComments() as $comment) /* @var $comment \Entity\Feed\Comment */{
			$feedComments[] = array (
				'CommentText' => $comment->getText(),
				'CommentDate' => $comment->getDateAdded()->getTimestamp(),
				'CommentUserName' => $comment->getAuthor()->getName(),
			);
		}
		
		$trainingPlanReports = array ();
		foreach ($post->getWorkout()->getTrainingPlanReports() as $trainingPlanReport) {
			$exercises = array ();
			foreach ($trainingPlanReport->getTrainingPlan()->getExercises() as $exercise) /* @var $exercise \Entity\Exercise */  {
			    $exercises[] = array (
			    	'ExerciseGoalDistance' => $exercise->getGoal() != null ? $exercise->getGoal()->getDistance() : null,
			    	'ExerciseGoalDuration' => $exercise->getGoal() != null ? $exercise->getGoal()->getDuration() : null,
			    );
			}
		    $trainingPlanReports[] = array (
				'TrainingPlanName' => $trainingPlanReport->getTrainingPlan()->getName(),
				'ReportDistance' => $trainingPlanReport->getDistance(),
				'ReportDuration' => $trainingPlanReport->getDuration(),
				'BurnedCalories' => $trainingPlanReport->getBurnedCalories(),
				'ReportID' => $trainingPlanReport->getTrainingPlan()->getId(),
				'ReportAverageSpeed' => $trainingPlanReport->getAverageSpeed(),
				'ReportAverageHeartRate' => $trainingPlanReport->getAverageHeartRate(),
				'WorkoutPlaylist' => $trainingPlanReport->getWorkout()->getPlayList(),
				'ReportTrainingPlanHasWorkoutGoal' =>  $trainingPlanReport->getTrainingPlan()->hasWorkoutGoal(),
				'ReportTrainingPlanIsChallenge' => $trainingPlanReport->getTrainingPlan()->isChallenge(),
				'Exercises' => $exercises,
			);
		}
				
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'FeedPostDetails' => array (
				'WorkoutID' => $post->getWorkout()->getId(),
				'WorkoutPlaylist' => $post->getWorkout()->getPlayList(),
				'UserWorkoutsNr' => $post->getAuthor()->getWorkouts()->count(),
				'UserDuration' => $post->getAuthor()->countTime(),
				'UserDistance' => $post->getAuthor()->countDistances(),
				'FeedComments' => $feedComments,
				'TrainingPlanReports' => $trainingPlanReports,
			),
		));
		
		return $response;
	}
	
	public function getNoteDetails($userId, $sessionId, $feedId) {
	    $userService = new User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    /* @var $post \Entity\Feed\Post\Workout */
	    $post = $this->newsFeedService->getPost($feedId);
	    if (!($post instanceof \Entity\Feed\Post\Note)) {
	    	throw new \Zend_Exception('Feed post is not Note post', 13);
	    }
	    
	    $feedComments = array ();
	    foreach ($post->getComments() as $comment) /* @var $comment \Entity\Feed\Comment */{
	    	$feedComments[] = array (
	    			'CommentText' => $comment->getText(),
	    			'CommentDate' => $comment->getDateAdded()->getTimestamp(),
	    			'CommentUserName' => $comment->getAuthor()->getName(),
	    	);
	    }
	    
	    $response = \Zend_Json::encode(array (
	    		'Response' => 'OK',
	    		'FeedPostDetails' => array (
    				'NoteID' => $post->getId(),
	    			'UserWorkoutsNr' => $user->getWorkouts()->count(),
	    			'UserDuration' => $user->countTime(),
	    			'UserDistance' => $user->countDistances(),
    				'FeedComments' => $feedComments,
	    		),
	    ));
	    
	    return $response;
	}
	
	public function unsignedFriendsListFB($userId, $sessionId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$friends = array ();
		foreach ($this->userService->getUnsignedFacebookFriends($user) as $facebookFriend) {
			$friends[] = array (
					'FriendUserId' => $facebookFriend['id'],
					'FriendName' => $facebookFriend['name'],
					'FriendImage' => $facebookFriend['profileImage'],
			);
		}
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Friends' => $friends,
		));
		 
		return $response;
	}
	
	public function unsignedFriendsListTwitter($userId, $sessionId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$friends = array ();
		foreach ($this->userService->getUnsignedTwitterFriends($user) as $twitterFriend) {
			$friends[] = array (
					'FriendUserId' => $twitterFriend['id'],
					'FriendName' => $twitterFriend['name'],
					'FriendImage' => $twitterFriend['profileImage'],
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Friends' => $friends,
		));
			
		return $response;
	}
	
	public function inviteFriends($userId, $sessionId, $friendsToInviteFBIDs, $friendsToInviteTwitterIDs, $friendsToInviteEMails) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		foreach (explode(',', $friendsToInviteFBIDs) as $friendId) {
			$this->userService->inviteFacebook($user, trim($friendId), 'I\'m using Trainingbook.com application on my phone. Please check it out at http://trainingbook.com.');
		}
		
		foreach (explode(',', $friendsToInviteTwitterIDs) as $friend) {
			$this->userService->inviteTwitter($user, trim($friendId), 'I\'m using Trainingbook.com application on my phone. Please check it out at http://trainingbook.com.');
		}
		
		foreach (explode(',', $friendsToInviteEMails) as $friend) {
		}
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
		));
	}
}