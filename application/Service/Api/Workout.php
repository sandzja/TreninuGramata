<?php

namespace Service\Api;

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
		$userService = new \Service\Api\User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$trainingPlanDTO = new \Service\DTO\TrainingPlan();
		$trainingPlanDTO->date = new \DateTime('@' . (int) $trainingPlan['trainingPlanDateCreated']);
		$trainingPlanDTO->name = $trainingPlan['trainingPlanName'];
		$trainingPlanDTO->sportId = $trainingPlan['sportID'];
		$trainingPlanDTO->isChallenge = $trainingPlan['isChallenge'];
		$trainingPlanDTO->hasWorkoutGoal = $trainingPlan['hasWorkoutGoal'];
		$trainingPlanDTO->note = $trainingPlan['note'];
		$trainingPlanDTO->userId = $userId;
		$trainingPlanDTO->isPrivate = true;
		
		foreach ($trainingPlan['exercises'] as $exercise) {
			$exerciseDTO = new \Service\DTO\Exercise();
			$exerciseDTO->name = $exercise['exerciseName'];
			$exerciseDTO->intensity = $exercise['exerciseIntensity'];
			$exerciseDTO->goalDistance = $exercise['goalDistance'];
			$exerciseDTO->goalDuration = $exercise['goalDuration'];
			$exerciseDTO->goalIsChallenge = $exercise['goalIsChallenge'];
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
		$userService = new \Service\Api\User();
		
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$workoutDTO = new \Service\DTO\Workout();
		$workoutDTO->trainingPlanId = $trainingPlanReport['trainingPlanID'];
		$workoutDTO->startTime = new \DateTime('@' . (int) $trainingPlanReport['startTime']);
		
		$report = $this->workoutService->saveTrainingPlanReport($this->workoutService->getWorkout($workoutId), $workoutDTO);
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'PlanReportID' => $report->getId(),
		));
		
		return $response;
	}
	
	public function planReportDone($userId, $sessionId, $trainingPlanReport) {
		$userService = new \Service\Api\User();
		
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
		
		$planReport = $this->workoutService->getTrainingPlanReport($trainingPlanReport['planReportID']);
		$planReport->setBurnedCalories($trainingPlanReport['calories']);
		$planReport->setEndTime(new \DateTime('@' . (int) $trainingPlanReport['endTime']));
		$planReport->setDuration($trainingPlanReport['duration']);
		$planReport->setDistance($trainingPlanReport['distance']);
		$planReport->setPlayList($trainingPlanReport['playlist']);
		
		$this->workoutService->persistTrainingPlanReport($planReport);
		
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

		$workout = $this->workoutService->saveWorkout($workoutDTO);
		
		$feedWorkout = new \Entity\Feed\Post\Workout();
		$feedWorkout->setComment('');
		$feedWorkout->setAuthor($user);
		$feedWorkout->setIsPrivate((boolean) $workoutDTO->isPrivate);
		$feedWorkout->setWorkout($workout);
		$this->newsFeedService->saveWorkoutPost($feedWorkout);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'WorkoutID' => $workout->getId(),
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
		$workoutEntity->setShared((boolean) $workout['isShared']);
		$workoutEntity->setRating($workout['rating']);
		$workoutEntity->getFeedPost()->setComment($workout['comment']);
		
		$this->workoutService->persistWorkout($workoutEntity);
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'WorkoutID' => $workoutEntity->getId(),
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
		$workoutService->persistWorkout($workout);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
	public function trackPoint($userId, $sessionId, $exerciseReportId, $trackPoint) {
		$userService = new User();
		$userService->checkAndUpdateSession($userId, $sessionId);
		
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
		
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
		));
		
		return $response;
	}

	public function getUserWorkouts($userId, $sessionId, $workoutUserId) {
		$userService = new \Service\Api\User();
		$user = $userService->checkAndUpdateSession($userId, $sessionId);
	
		$workouts = array ();
		
		foreach ($this->userService->getUser($workoutUserId)->getWorkouts() as $workout) /* @var $workout \Entity\Workout */ {
			$planReportIds = array ();
			foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) {
				$planReportIds[] = $trainingPlanReport->getid();
			}
			$workouts[] = array (
				'WorkoutID' => $workout->getId(),
				'WorkoutName' => $workout->getName(),
				'PlanReportIDs' => $planReportIds,
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
		$userService = new \Service\Api\User();
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
					'WorkoutID' => $record->getWorkout()->getId(),
			);
		}
	
		$response = \Zend_Json::encode(array (
				'Response' => 'OK',
				'Workouts' => $records,
		));
	
		return $response;
	}
	
}