<?php

namespace Service\Api2;

use Service\ServiceManager;

class Workout {
	
	/**
	 * @var \Service\TrainingPlan
	 */
	private $trainingPlanService;
	
	/**
	 * @var \Service\Workout
	 */
	private $workoutService;
	
	/**
	 * @var \Service\NewsFeed
	 */
	private $newsFeedService;
	
	/**
	 * @var \Service\Exercise
	 */
	private $exerciseService;
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function __construct() {
		$this->trainingPlanService = ServiceManager::factory(new \Service\TrainingPlan());
		$this->workoutService = ServiceManager::factory(new \Service\Workout());
		$this->newsFeedService = ServiceManager::factory(new \Service\NewsFeed());
		$this->exerciseService = ServiceManager::factory(new \Service\Exercise());
		$this->userService = ServiceManager::factory(new \Service\User());
	}
	
	public function createTrainingPlan($userId, $sessionId, $trainingPlan) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$trainingPlanDTO = new \Service\DTO\TrainingPlan();
		$trainingPlanDTO->date = new \DateTime('@' . (int) $trainingPlan['trainingPlanDateCreated']);
		$trainingPlanDTO->name = $trainingPlan['trainingPlanName'];
		$trainingPlanDTO->sportId = $trainingPlan['sportID'];
		$trainingPlanDTO->isChallenge = $trainingPlan['isChallenge'];
		$trainingPlanDTO->hasWorkoutGoal = $trainingPlan['hasWorkoutGoal'];
		$trainingPlanDTO->note = $trainingPlan['note'];
		$trainingPlanDTO->userId = $userId;
		$trainingPlanDTO->isPrivate = false;
		$trainingPlanDTO->synced = true;
		
		foreach ($trainingPlan['exercises'] as $exercise) {
			$exerciseDTO = new \Service\DTO\Exercise();
			$exerciseDTO->name = $exercise['exerciseName'];
			$exerciseDTO->intensity = $exercise['exerciseIntensity'];
			$exerciseDTO->goalDistance = $exercise['goalDistance'];
			$exerciseDTO->goalDuration = $exercise['goalDuration'];
			$exerciseDTO->goalIsChallenge = $exercise['goalIsChallenge'];
			if (isset($exercise['exerciseNote'])) {
				$exerciseDTO->note = $exercise['exerciseNote'];
			}
			$exerciseDTO->synced = true;
			$trainingPlanDTO->exercises[] = $exerciseDTO;
		}
		
		$trainingPlanFeed = $this->newsFeedService->saveTrainingPlan($trainingPlanDTO);
		
		$exerciseIds = array ();
		foreach ($trainingPlanFeed->getTrainingPlan()->getExercises() as $exercise) {
			$exerciseIds[] = $exercise->getId();
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'TrainingPlanID' => $trainingPlanFeed->getTrainingPlan()->getId(),
			'Exercises' => $exerciseIds,
		));
		
		return $response;
	}
	
	public function planReportStarted($userId, $sessionId, $workoutId, $trainingPlanReport) {
		$userService = new User();
		
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workoutDTO = new \Service\DTO\Workout();
		$workoutDTO->trainingPlanId = $trainingPlanReport['trainingPlanID'];
		$workoutDTO->startTime = new \DateTime('@' . (int) $trainingPlanReport['startTime']);
		$workoutDTO->synced = true;
		
		$report = $this->workoutService->saveTrainingPlanReport($this->workoutService->getWorkout($workoutId), $workoutDTO);
		
		if ($report->getWorkout()->isShared()) {
			if ($user->getFacebookUserId() != null) {
				$this->newsFeedService->postLiveTrackingToFacebook($report->getWorkout(), $user);
			}
			
			if ($user->getTwitterUserId() != null) {
				$this->newsFeedService->postLiveTrackingToTwitter($report->getWorkout());
			}
		}
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'PlanReportID' => $report->getId(),
		));
		
		return $response;
	}
	
	public function planReportDone($userId, $sessionId, $trainingPlanReport) {
		$userService = new User();
		
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$planReport = $this->workoutService->getTrainingPlanReport($trainingPlanReport['planReportID']);
		$planReport->setBurnedCalories(@$trainingPlanReport['calories']);
		$planReport->setEndTime(new \DateTime('@' . (int) $trainingPlanReport['endTime']));
		$planReport->setDuration($trainingPlanReport['duration']);
		$planReport->setDistance($trainingPlanReport['distance']);
		$planReport->setSynced(true);
		
		$this->workoutService->persistTrainingPlanReport($planReport);
		
// 		if ($user->getFacebookUserId() != null) {
// 			$this->newsFeedService->postWorkoutToFacebook($planReport->getWorkout());
// 		}
		
// 		if ($user->getTwitterUserId() != null) {
// 			$this->newsFeedService->postWorkoutToTwitter($planReport->getWorkout());
// 		}
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function exerciseReportStarted($userId, $sessionId, $exerciseReport) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workoutDTO = new \Service\DTO\Workout();
		$workoutDTO->startTime = new \DateTime('@' . (int) $exerciseReport['startTime']);
		$workoutDTO->exerciseId = (int) $exerciseReport['exerciseID'];
		$workoutDTO->synced = true;
		
		$report = $this->workoutService->startExerciseReport($workoutDTO, $this->workoutService->getTrainingPlanReport($exerciseReport['planReportID']));
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'ExerciseReportID' => $report->getId(),
		));
		
		return $response;
	}
	
	public function deleteTrainingPlan($TrainingPlanDelete) {
		$userService = new User();
		$trainingPlanService = new \Service\TrainingPlan();

		$user = $userService->checkAndUpdateSession(@$TrainingPlanDelete['UserID'], @$TrainingPlanDelete['SessionID']);
		
		$trainingPlan = $trainingPlanService->fetch(@$TrainingPlanDelete['TrainingPlanID']);
		$trainingPlanService->deleteTrainingPlan($trainingPlan, new \DateTime(@$TrainingPlanDelete['TrainingPlanDeleted']));
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function startWorkout($userId, $sessionId, $workout) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workoutDTO = new \Service\DTO\Workout();
		$workoutDTO->name = $workout['workoutName'];
		$workoutDTO->startTime = new \DateTime('@' . (int) $workout['workoutStartTime']);
		$workoutDTO->userId = $userId;
		$workoutDTO->isPrivate = (!(boolean) $workout['workoutIsShared']);
		$workoutDTO->synced = true;

		$workoutEntity = $this->workoutService->saveWorkout($workoutDTO);
		
		$feedWorkout = new \Entity\Feed\Post\Workout();
		$feedWorkout->setComment('');
		$feedWorkout->setAuthor($user);
		$feedWorkout->setIsPrivate(false);
		$feedWorkout->setWorkout($workoutEntity);
		$this->newsFeedService->saveWorkoutPost($feedWorkout);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'WorkoutID' => $workoutEntity->getId(),
		));
		
		return $response;
	}
	
	public function workoutDone($userId, $sessionId, $workout) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workoutEntity = $this->workoutService->getWorkout($workout['workoutID']);
		$workoutEntity->setDistance($workout['distance']);
		$workoutEntity->setEndTime(new \DateTime('@' . (int) $workout['endTime']));
		$workoutEntity->setDuration($workout['duration']);
		$workoutEntity->setRating($workout['rating']);
		$workoutEntity->setSynced(true);
		$workoutEntity->getFeedPost()->setComment($workout['comment']);
		$workoutEntity->getFeedPost()->setSendFacebook((boolean) $workout['facebook']);
		$workoutEntity->getFeedPost()->setSendTwitter((boolean) $workout['twitter']);
		$workoutEntity->getFeedPost()->setIsPrivate(!(boolean) $workout['isShared']);

		$this->workoutService->persistWorkout($workoutEntity);
		
		$multipleWorkouts = $workoutEntity->getTrainingPlanReports()->count() > 1;
		
		if ($workout['facebook'] != 0) {
		    $this->newsFeedService->postWorkoutToFacebook($workoutEntity, $user, $multipleWorkouts);
		}
		
    	if ($workout['twitter'] != 0) {
			$this->newsFeedService->postWorkoutToTwitter($workoutEntity, $multipleWorkouts, $user);
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'WorkoutID' => $workoutEntity->getId(),
			'FeedID' => $workoutEntity->getFeedPost()->getId(),
		));
		
		return $response;
	}
	
	public function exerciseReportDone($userId, $sessionId, $exerciseReport) {
		$userService = new User();
		$userService->checkAndUpdateSession($userId, $sessionId);
		
		
		$exerciseReportEntity = $this->exerciseService->getReport(@$exerciseReport['exerciseReportID']);
		$exerciseReportEntity->setDistance($exerciseReport['distance']);
		$exerciseReportEntity->setDuration($exerciseReport['duration']);
		$exerciseReportEntity->setEndTime(new \DateTime('@' . (int) $exerciseReport['endTime']));
		$exerciseReportEntity->setSynced(true);
		
		$this->exerciseService->persistReport($exerciseReportEntity);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function updateWorkoutSummary($WorkoutDone) {
		$userService = new User();
		$userService->checkAndUpdateSession(@$WorkoutDone['UserID'], @$WorkoutDone['SessionID']);
		
		$workoutService = new \Service\Workout();
		
		$workout = $workoutService->fetch(@$WorkoutDone['WorkoutID']);
		$workout->setDistance(@$WorkoutDone['WorkoutDistance']);
		$endTime = new \DateTime();
		$endTime->setTimestamp(@$WorkoutDone['WorkoutEndTime']);
		$workout->setEndTime($endTime);
		$workout->setSynced(true);
		$workoutService->persistWorkout($workout);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function trackPoint($userId, $sessionId, $exerciseReportId, $trackPoint) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$exerciseReport = $this->exerciseService->getReport($exerciseReportId);
		
		$trackPointEntity = new \Entity\Exercise\TrackPoint();
		$trackPointEntity->setTime(new \DateTime('@' . (int) $trackPoint['time']));
		$trackPointEntity->setAlt($trackPoint['altitude']);
		$trackPointEntity->setLon($trackPoint['longitude']);
		$trackPointEntity->setLat($trackPoint['latitude']);
		$trackPointEntity->setDistanceToLastPoint($trackPoint['distanceToLastPoint']);
		$trackPointEntity->setSpeed($trackPoint['speed']);
		$trackPointEntity->setHeart($trackPoint['heart']);
		$trackPointEntity->setReport($exerciseReport);
		$trackPointEntity->setIsUploaded(true);
		$this->exerciseService->persistTrackPoint($trackPointEntity);
		
		/* @var $messageService \Service\Message */
		$messageService = ServiceManager::factory(new \Service\Message());
		
		$messages = array ();
		foreach ($messageService->recieve($user, new \DateTime('@' . (int) $trackPoint['time'])) as $message) /* @var $message \Entity\Message */ {
		    $messages[] = $message->getMessage();
		}
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'PepTalk' => $messages,
		));
		
		return $response;
	}
	
	public function cancelWorkout($userId, $sessionId, $workoutID) {
	    $userService = new User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    if ($this->workoutService->getWorkout($workoutID)->getEndTime() != null) {
	        throw new \Zend_Exception('End time has set', 14);
	    }
	    
	    $workout = $this->workoutService->getWorkout($workoutID);
	    
	    $this->workoutService->removeWorkout($workout);
	    
	    $response = \Zend_Json::encode(array (
				'Response' => 'OK',
		));
		
		return $response;
	}

	public function getUserWorkouts($userId, $sessionId, $workoutUserId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$workouts = array ();
		
		foreach ($this->userService->getUser($workoutUserId)->getWorkouts() as $workout) /* @var $workout \Entity\Workout */ {
			$trainingPlanReports = array ();
			foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
			    $trainingPlanReports[] = array (
			    	'TrainingPlanName' => $trainingPlanReport->getTrainingPlan()->getName(),
			    	'SportID' => $trainingPlanReport->getTrainingPlan()->getSport()->getId(),
			    	'SportName' => $trainingPlanReport->getTrainingPlan()->getSport()->getName(),
			    	'ReportDistance' => $trainingPlanReport->getDistance(),
			    	'TrainingPlanReportID' => $trainingPlanReport->getId(),
			    );
			}
			$workouts[] = array (
				'FeedID' => $workout->getFeedPost()->getId(),
				'FeedNote' => $workout->getFeedPost()->getComment(),
				'WorkoutRating' => $workout->getRating(),
				'WorkoutID' => $workout->getId(),
				'WorkoutName' => $workout->getName(),
				'TrainingPlanReports' => $trainingPlanReports,
				'WorkoutDistance' => $workout->getDistance(),
				'WorkoutDuration' => $workout->getDuration(),
				'WorkoutStartTime' => $workout->getStartTime() != null ? $workout->getStartTime()->getTimestamp() : null,
				'WorkoutEndTime' => $workout->getEndTime() != null ? $workout->getEndTime()->getTimestamp() : null,
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Workouts' => $workouts,
		));
	
		return $response;
	}
	
	public function getUserRecords($userId, $sessionId, $recordUserId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$records = array ();
		foreach ($this->userService->getUser($recordUserId)->getRecords() as $record) /* @var $record \Entity\Record */ {
			$records[] = array (
					'RecordID' => $record->getId(),
					'RecordDistance' => $record->getDistance(),
					'RecordDuration' => $record->getDuration(),
					'RecordIsTimeRecord' => $record->isTimeRecord(),
					'RecordIsMiles' => $record->isMiles(),
					'SportID' => $record->getSport()->getId(),
					'SportName' => $record->getSport()->getName(),
					'WorkoutID' => $record->getWorkout()->getId(),
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Records' => $records,
		));
	
		return $response;
	}
	
	public function createRecord($userId, $sessionId, $record) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$recordEntity = new \Entity\Record();
		
		$recordEntity->setDistance($record['distance']);
		$recordEntity->setDuration($record['duration']);
		$recordEntity->setIsMiles((boolean) $record['isMiles']);
		$recordEntity->setIsTimeRecord((boolean) $record['isTimeRecord']);
		$recordEntity->setSport($this->workoutService->getSport($record['sportId']));
		$recordEntity->setSynced(true);
		$recordEntity->setUser($user);
		$recordEntity->setWorkout($this->workoutService->getWorkout($record['workoutId']));
		
		$this->workoutService->persistRecord($recordEntity);
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'RecordID' => $recordEntity->getId(),
		));
	
		return $response;
	}
	
	public function deleteRecord($userId, $sessionId, $recordId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$record = $this->workoutService->getRecord($recordId);
		
		$this->workoutService->removeRecord($record);
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
		));
	
		return $response;
	}
	
	public function getTrainingPlan($userId, $sessionId, $trainingPlanId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$trainingPlan = $this->workoutService->getTrainingPlan($trainingPlanId);
		
		$exerciseIds = array ();
		foreach ($trainingPlan->getExercises() as $exercise) {
			$exerciseIds[] = $exercise->getId();
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'TrainingPlanDateCreated' => $trainingPlan->getDate()->getTimestamp(),
			'TrainingPlanName' => $trainingPlan->getName(),
			'SportID' => $trainingPlan->getSport()->getId(),
			'IsChallenge' => $trainingPlan->isChallenge(),
			'HasWorkoutGoal' => (boolean) $trainingPlan->hasWorkoutGoal(),
			'Note' => $trainingPlan->getFeedPost()->getComment(),
			'ExerciseIDs' => $exerciseIds,
		));
		
		return $response;
	}
	
	public function getTrainingPlanReport($userId, $sessionId, $trainingPlanReportId) {
		$userService = new User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$trainingPlanReport = $this->workoutService->getTrainingPlanReport($trainingPlanReportId);
		
		$exerciseReportIds = array ();
		foreach ($trainingPlanReport->getExerciseReports() as $exerciseReport) {
			$exerciseReportIds[] = $exerciseReport->getId();
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'TrainingPlanId' => $trainingPlanReport->getTrainingPlan()->getId(),
			'StartTime' => $trainingPlanReport->getStartTime()->getTimestamp(),
			'EndTime' => $trainingPlanReport->getEndTime()->getTimestamp(),
			'Duration' => $trainingPlanReport->getDuration(),
			'Distance' => $trainingPlanReport->getDistance(),
			'Calories' => $trainingPlanReport->getBurnedCalories(),
			'ChallengeReportID' => $trainingPlanReport->getChallengeReport()->getId(),
			'ExerciseReportIDs' => $exerciseReportIds,
		));
		
		return $response;
	}

	public function getTrainingPlansForSport($userId, $sessionId, $trainingPlanUserId, $sportId) {
		$userService = new \Service\Api\User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$trainingPlans = $this->workoutService->getTrainingPlansByUserAndSport($trainingPlanUserId, $sportId);
		
		$trainingPlanResponse = array ();
		foreach ($trainingPlans as $trainingPlan) /* @var $trainingPlan \Entity\TrainingPlan */ {
			$exerciseIds = array ();
			foreach ($trainingPlan->getExercises() as $exercise) {
				$exerciseIds[] = $exercise->getId();
			}
			
			$trainingPlanResponse[] = array (
				'TrainingPlanDateCreated' => $trainingPlan->getDate()->getTimestamp(),
				'TrainingPlanName' => $trainingPlan->getName(),
				'SportID' => $trainingPlan->getSport()->getId(),
				'IsChallenge' => (boolean) $trainingPlan->isChallenge(),
				'HasWorkoutGoal' => (boolean) $trainingPlan->hasWorkoutGoal(),
				'Note' => $trainingPlan->getFeedPost()->getComment(),
				'ExerciseIDs' => $exerciseIds,
			);
		}
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'TrainingPlans' => $trainingPlanResponse,
		));
		
		return $response;
	}
	
	
	public function getUserWorkoutsForSport($userId, $sessionId, $workoutUserId, $sportId) {
		$userService = new \Service\Api\User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workouts = $this->workoutService->getWorkoutsByUserAndSport($workoutUserId, $sportId);
		
		$workoutResponses = array ();
		foreach ($workouts as $workout) /* @var $workout \Entity\Workout */ {
			$planReportIds = array ();
			foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) {
				$planReportIds[] = $trainingPlanReport->getId();
			}
			$workoutResponses[] = array (
				'WorkoutID' => $workout->getId(),
				'WorkoutName' => $workout->getName(),
				'PlanReportIds' => $planReportIds,
				'WorkoutDistance' => $workout->getDistance(),
				'WorkoutDuration' => $workout->getDuration(),
				'WorkoutStartTime' => $workout->getStartTime() != null ? $workout->getStartTime()->getTimestamp() : null,
				'WorkoutEndTime' => $workout->getEndTime() != null ? $workout->getEndTime()->getTimestamp() : null,
			);
		}
		
			
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'Workouts' => $workoutResponses,
		));
		
		return $response;
	}
	
	
	public function startChallenge($userId, $sessionId, $challenge) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$challengeDTO = new \Service\DTO\Challenge();
		$challengeDTO->date = new \DateTime('@' . (int) $challenge['date']);
		$challengeDTO->opponentUserId = $challenge['opponentID'];
		$challengeDTO->recordId = $challenge['recordID'];
		$challengeDTO->workoutId = $challenge['workoutID'];
		$challengeDTO->trainingPlanId = $challenge['trainingPlanID'];
		$challengeDTO->userId = $userId;

		$challengeEntity = $this->workoutService->saveChallenge($challengeDTO);
		
		$feedChallenge = new \Entity\Feed\Post\Challenge();
		$feedChallenge->setComment('');
		$feedChallenge->setAuthor($user);
		$feedChallenge->setIsPrivate(true);
		$feedChallenge->setChallenge($challengeEntity);
		$this->newsFeedService->saveChallengePost($feedChallenge);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'ChallengeID' => $challengeEntity->getId(),
		));
		
		return $response;
	}
	
	
	public function challengeReport($userId, $sessionId, $challengeReport) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$challengeDTO = new \Service\DTO\Challenge();
		$challengeDTO->didWinChallenge = (boolean) $challengeReport['didWinChallenge'];
		$challengeDTO->trainingPlanReportId = $challengeReport['trainingPlanReportID'];
		$challengeDTO->challengeId = $challengeReport['challengeID'];

		$challengeReportEntity = $this->workoutService->saveChallengeReport($challengeDTO);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'ChallengeReportID' => $challengeReportEntity->getId(),
		));
		
		return $response;
	}

	
	public function getExercise($userId, $sessionId, $exerciseId) {
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$exercise = $this->workoutService->getExercise($exerciseId);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'Exercise' => array (
				'ExerciseName' => $exercise->getName(),
				'ExerciseIntensity' => $exercise->getIntensity(),
				'GoalDistance' => $exercise->getGoal()->getDistance(),
				'GoalDuration' => $exercise->getGoal()->getDuration(),
				'GoalIsChallenge' => $exercise->getGoal()->isChallenge(),
			),
		));
		
		return $response;
	}
}