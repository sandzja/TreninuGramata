<?php

namespace Service\Api2;

use Service\ServiceManager;

function gzdecode($data){
	$g=tempnam('/tmp','ff');
	@file_put_contents($g,$data);
	ob_start();
	readgzfile($g);
	$d=ob_get_clean();
	return $d;
}

class Sync {
	
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
	
	private function getParam(array $array, $parameter) {
	    if (!is_array($array) || !array_key_exists($parameter, $array)) {
	        throw new \Zend_Exception('Incorrect request parameters. Missing "' . $parameter . '".', 1);
	    }
	    
	    return $array[$parameter];
	}
	
	public function synchronize($userId, $sessionId, $data) {
// 	    $data = 'H4sIAAAAAAAAA%2B2ZQXOiMBiG%2FwrDmTJJSIBwXG13PWynY53pYaeHFLMtU0wcEsY6Hf%2F7BgTForW2dHsoF0dC8n1fXp7kjfhsTzKWiETcX6VMKDv687zTMmSaDzJuPqd2BD3k0wBjil0IEAw8xx6pwQNLUy7uuR2JPE0d%2B3ouMz0a2hF27F9M3cjsUeb6p2Rp3aOZ4JLNzEj7B1NJbOmMxY%2Fmhu3Y5088ixPF1yXVVyOhuVCJXtoR2PapYky2o4tswzxjOpGizlq2JUozEfNm284U%2FrJU8dWtY19KXfVaOW%2BTBEEPU9SdJOdMLa3FuutbBMEtQW5YNrPyeUsP8FIMD4CDWjh7k8F2MlOpNX9gih%2FPB0%2FO137aY6705%2BVrizmQMrWmciE%2Blu5daGHqkSDsDq3ffJrks8%2BBy2%2FJcar26BS2YPjhfG2WX2Hrf08PfTjfKSifMrt3kRwCCgh%2BnWT0vXwjpB6gQXeS9L7R%2B0aJFnEhgX6HZPW20dvGV9gGcTEhPj5yAPK%2Bk20Ql2APhaQ7SXrb6G2jQisEAKAOV1vvG71vfIVv%2BC4EAfCPkAy%2Fk28YSSj1jvnGKZL0vtH7RoUWJjigsDu0et%2FofaNj37itUFwTVHMmc6ENfdZdUm3Mm7qu57yAPHTNeWfAUpklXF2wWMusaCTN96%2FFvOrKl3FqAlmquLknHIQuPBYPNeJdJFpwpawFSw8UCF3cDug3z2uNcONciP1hkBu2wwTN5VsoOOaxzKaFhOaiWsprQc0QfiflY0nrztMZyNmMC12v9nMxnSRFMWb7MBYdhGFYbB9e8SLM7ADL1Izd7B2aZfpFb98FPvUR3uRvMGquLAQgOgPwDAELkoiEZq6TRaI1z9albVBCuzvPmG%2FpaLaPxJQ%2F2RHd7V0%2BJ1r8MK3RbY7fQD58fSbmCwX%2Bi6J2xCuPDFcyEboSfbt%2ByrLAIUEJRn7J%2FN7EEHrFH1aH8%2B4LSiAFYQM207FkwoQwSBWjRur6gWUllavb1T%2FGMMa%2BYhwAAA%3D%3D';
// 	    $data = urldecode($data);
	    
	    $data = (gzdecode(base64_decode(($data))));
		$userService = new User();

		$user = $userService->checkAndUpdateSession($userId, $sessionId);
// 		$userId = 54;
// 		$user = $this->userService->getUser(54);
		
		$data = json_decode($data, true);
		
		$idData = array ();

		$sportResponse = array ();
		foreach ($data['Sports'] as $sport) {
		    if ($this->getParam($sport, 'SportID') != null) {
		        $sportEntity = $this->workoutService->getSport($this->getParam($sport, 'SportID'));
		    } else {
		    	$sportEntity = new \Entity\Sport();
		    }
		    $sportEntity->setName($this->getParam($sport, 'Name'));
		    $sportEntity->setCaloriesFactor($this->getParam($sport, 'CaloriesFactor'));
		    $sportEntity->setIntensitySpeed($this->getParam($sport, 'IntensitySpeed'));
		    $sportEntity->setSynced(true);
		    $sportEntity->setUser($user);
		    
		    $this->workoutService->getEntityManager()->persist($sportEntity);
		    
		    $sportResponse[] = array (
		    		'SportID' => $sportEntity->getId(),
		    );
		}
		
		$trainingPlanResponse = array ();
		foreach ($data['TrainingPlans'] as $trainingPlanIndex => $trainingPlan) {
		    $trainingPlanDTO = new \Service\DTO\TrainingPlan();
		    $trainingPlanDTO->date = new \DateTime('@' . (int) $this->getParam($trainingPlan, 'TrainingPlanDateCreated'));
		    $trainingPlanDTO->name = $this->getParam($trainingPlan, 'TrainingPlanName');
		    $trainingPlanDTO->sportId = $this->getParam($trainingPlan, 'SportID');
		    $trainingPlanDTO->hasWorkoutGoal = (boolean) $this->getParam($trainingPlan, 'HasWorkoutGoal');
		    $trainingPlanDTO->note = $this->getParam($trainingPlan, 'Note');
		    $trainingPlanDTO->userId = $userId;
		    $trainingPlanDTO->isPrivate = false;
		    $trainingPlanDTO->synced = true;
		    if (isset($trainingPlan[TrainingPlanDeleted])) {
		    	$trainingPlanDTO->deletedTime = new \DateTime('@' . (int) $this->getParam($trainingPlan, 'TrainingPlanDeleted'));
		    }
		    
		    $trainingPlanFeed = $this->newsFeedService->saveTrainingPlan($trainingPlanDTO);
		    $trainingPlanEntity = $trainingPlanFeed->getTrainingPlan();
		    
		    $exerciseIds = array ();
		    foreach ($this->getParam($trainingPlan, 'Exercises') as $exercise) {
		    	$exerciseEntity = new \Entity\Exercise();
		    	$exerciseEntity->setName($this->getParam($exercise, 'ExerciseName'));
		    	$exerciseEntity->setIntensity($this->getParam($exercise, 'ExerciseIntensity'));
		    	$exerciseEntity->setNote($this->getParam($exercise, 'ExerciseNote'));
		    	$exerciseEntity->setSynced(true);
		    	$goal = new \Entity\Goal();
		    	$goal->setDistance($this->getParam($exercise, 'GoalDistance'));
		    	$goal->setDuration($this->getParam($exercise, 'GoalDuration'));
		    	$goal->setChallenge((boolean) $this->getParam($exercise, 'GoalIsChallenge'));
		    	$goal->setSynced(true);
		    	$exerciseEntity->setGoal($goal);
		    	$exerciseEntity->setTrainingPlan($trainingPlanEntity);
		    	
		    	$trainingPlanEntity->addExercise($exerciseEntity);
		    	$this->workoutService->getEntityManager()->persist($exerciseEntity);
		    	
		    	$exerciseIds[] = array (
		    		'ExerciseID' => $exerciseEntity->getId(),
		    	);
		    	
		    	$idData['TrainingPlans'][$trainingPlanIndex][] = $exerciseEntity;
		    }
		    
		    $trainingPlanResponse[] = array (
		    	'TrainingPlanID' => $trainingPlanFeed->getTrainingPlan()->getId(),
		    	'Exercises' => $exerciseIds,
		    );
		}
		
		$workoutResponse = array ();
		foreach ($this->getParam($data, 'Workouts') as $workout) {
		    $workoutDTO = new \Service\DTO\Workout();
		    $workoutDTO->name = $this->getParam($workout, 'WorkoutName');
		    $workoutDTO->startTime = new \DateTime('@' . (int) $this->getParam($workout, 'StartTime'));
		    $workoutDTO->endTime = new \DateTime('@' . (int) $this->getParam($workout, 'EndTime'));
		    $workoutDTO->duration = $this->getParam($workout, 'Duration');
		    $workoutDTO->distance = $this->getParam($workout, 'Distance');
		    $workoutDTO->isPrivate = !((boolean) $this->getParam($workout, 'IsShared'));
		    $workoutDTO->rating = $this->getParam($workout, 'Rating');
		    $workoutDTO->comment = $this->getParam($workout, 'Comment');
		    $workoutDTO->sendFacebook = (boolean) $this->getParam($workout, 'Facebook');
		    $workoutDTO->sendTwitter = (boolean) $this->getParam($workout, 'Twitter');
		    $workoutDTO->playlist = $this->getParam($workout, 'Playlist');
		    $workoutDTO->userId = $userId;
		    $workoutDTO->synced = true;
		    
		    
		    $workoutEntity = $this->workoutService->saveWorkout($workoutDTO);
		    
		    $feedWorkout = new \Entity\Feed\Post\Workout();
		    $feedWorkout->setComment($this->getParam($workout, 'Comment'));
		    $feedWorkout->setAuthor($user);
		    $feedWorkout->setIsPrivate((boolean) $workoutDTO->isPrivate);
		    $feedWorkout->setWorkout($workoutEntity);
		    $this->newsFeedService->saveWorkoutPost($feedWorkout);
		    
		    $trainingPlanReportResponse = array ();
		    foreach ($this->getParam($workout, 'TrainingPlanReports') as $trainingPlanReport) {
		        if ($this->getParam($trainingPlanReport, 'TrainingPlanID') == '') {
		            $trainingPlanEntity = $this->workoutService->getTrainingPlan($trainingPlanResponse[$this->getParam($trainingPlanReport, 'TrainingPlanIndex')]['TrainingPlanID']);
		        } else {
		            $trainingPlanEntity = $this->workoutService->getTrainingPlan($this->getParam($trainingPlanReport, 'TrainingPlanID'));
		        }
		        
		        $trainingPlanReportEntity = new \Entity\TrainingPlan\Report();
		        $trainingPlanReportEntity->setStartTime(new \DateTime('@' . (int) $this->getParam($trainingPlanReport, 'StartTime')));
		        $trainingPlanReportEntity->setEndTime(new \DateTime('@' . (int) $this->getParam($trainingPlanReport, 'EndTime')));
		        $trainingPlanReportEntity->setDuration($this->getParam($trainingPlanReport, 'Duration'));
		        $trainingPlanReportEntity->setDistance($this->getParam($trainingPlanReport, 'Distance'));
		        $trainingPlanReportEntity->setBurnedCalories($this->getParam($trainingPlanReport, 'Calories'));
		        $trainingPlanReportEntity->setTrainingPlan($trainingPlanEntity);
		        $trainingPlanReportEntity->setWorkout($workoutEntity);
		        $trainingPlanReportEntity->setSport($trainingPlanEntity->getSport());
		        $trainingPlanReportEntity->setSynced(true);
		        
		        $this->workoutService->getEntityManager()->persist($trainingPlanReportEntity);
		        
		        $exerciseReportsResponse = array ();
		        foreach ($this->getParam($trainingPlanReport, 'ExerciseReports') as $exerciseReport) {
		            if ($this->getParam($exerciseReport, 'ExerciseID') == '') {
		                $exerciseEntity = $idData['TrainingPlans'][$this->getParam($trainingPlanReport, 'TrainingPlanIndex')][$this->getParam($exerciseReport, 'ExerciseIndex')];
		            } else {
		                $exerciseEntity = $this->workoutService->getExercise($this->getParam($exerciseReport, 'ExerciseID'));
		            }
		            
		            $exerciseReportEntity = new \Entity\Exercise\Report();
		            $exerciseReportEntity->setStartTime(new \DateTime('@' . (int) $this->getParam($exerciseReport, 'StartTime')));
		      	 	$exerciseReportEntity->setEndTime(new \DateTime('@' . (int) $this->getParam($exerciseReport, 'EndTime')));
		      	 	$exerciseReportEntity->setDuration($this->getParam($exerciseReport, 'Duration'));
		      	 	$exerciseReportEntity->setDistance($this->getParam($exerciseReport, 'Distance'));
		      	 	$exerciseReportEntity->setExercise($exerciseEntity);
		      	 	$exerciseReportEntity->setTrainingPlanReport($trainingPlanReportEntity);
		      	 	$exerciseReportEntity->setSynced(true);
		      	 	$trainingPlanReportEntity->addExerciseReport($exerciseReportEntity);
		      	 	
		      	 	$this->workoutService->getEntityManager()->persist($exerciseReportEntity);
		      	 	
		      	 	foreach ($this->getParam($exerciseReport, 'TrackPoints') as $trackPoint) {
		      	 	    $trackPointEntity = new \Entity\Exercise\TrackPoint();
		      	 	    $trackPointEntity->setTime(new \DateTime('@' . (int) $this->getParam($trackPoint, 'Time')));
		      	 	    $trackPointEntity->setAlt($this->getParam($trackPoint, 'Altitude'));
		      	 	    $trackPointEntity->setLat($this->getParam($trackPoint, 'Latitude'));
		      	 	    $trackPointEntity->setLon($this->getParam($trackPoint, 'Longitude'));
		      	 	    $trackPointEntity->setDistanceToLastPoint($this->getParam($trackPoint, 'DistanceToLastPoint'));
		      	 	    $trackPointEntity->setSpeed($this->getParam($trackPoint, 'Speed'));
		      	 	    $trackPointEntity->setHeart($this->getParam($trackPoint, 'Heart'));
		      	 	    $trackPointEntity->setIsUploaded(true);
		      	 	    $trackPointEntity->setReport($exerciseReportEntity);
		      	 	    
		      	 	    $this->workoutService->getEntityManager()->persist($trackPointEntity);
		      	 	}
		      	 	
		      	 	$exerciseReportsResponse[] = array (
		      	 		'ExerciseReportID' => $exerciseReportEntity->getId(),
		      	 		'ExerciseID' => $exerciseEntity->getId(),
		      	 	);
		        }
		        $trainingPlanReportResponse[] = array (
		        	'TrainingPlanReportID' => $trainingPlanReportEntity->getId(),
		        	'TrainingPlanID' => $trainingPlanEntity->getId(),
		        	'ExerciseReports' => $exerciseReportsResponse,
		        );
		    }
		    $workoutResponse[] = array (
		    	'WorkoutID' => $workoutEntity->getId(),
		    	'TrainingPlanReports' => $trainingPlanReportResponse,
		    );
		}
		
		$recordResponse = array ();
		foreach ($this->getParam($data, 'Records') as $record) {
			if ($this->getParam($record, 'WorkoutID') == '') {
			    $workoutEntity = $this->workoutService->getWorkout($workoutResponse[$this->getParam($record, 'WorkoutIndex')]['WorkoutID']);
			} else {
			    $workoutEntity = $this->workoutService->getWorkout($this->getParam($record, 'WorkoutID'));
			}
			
			if ($this->workoutService->getSport($this->getParam($record, 'SportID')) != null) {
				$recordEntity = new \Entity\Record();
				$recordEntity->setDuration($this->getParam($record, 'Duration'));
				$recordEntity->setDistance($this->getParam($record, 'Distance'));
				$recordEntity->setIsTimeRecord((boolean) $this->getParam($record, 'IsTimeRecord'));
				$recordEntity->setIsMiles((boolean) $this->getParam($record, 'IsMiles'));
				$recordEntity->setWorkout($workoutEntity);
				$recordEntity->setUser($user);
				$recordEntity->setSport($this->workoutService->getSport($this->getParam($record, 'SportID')));
				$recordEntity->setSynced(true);
			}
			
			$this->workoutService->persistRecord($recordEntity);
			
			$recordResponse[] = array (
				'RecordID' => $recordEntity->getId(),
				'WorkoutID' => $workoutEntity->getId(),
			);
		}
		
		/*------------------------------------------------------------------------*/
		
		$responseData = array ();
		
		$entityManager = $this->workoutService->getEntityManager();
		
		$sports = $entityManager->getRepository('\Entity\Sport')->getDefaultAndUserSportsNotSynced($user->getId());
		
		$sportsData = array ();
		foreach ($sports as $sport) /*@var $sport \Entity\Sport */ {
		    $sportsData[] = array (
		    	'SportID' => $sport->getId(),
		    	'Name' => $sport->getName(),
		    	'IntensitySpeed' => (double) $sport->getIntensitySpeed(),
		    	'CaloriesFactor' => (double) $sport->getCaloriesFactor(),
		    );
		}
		
		$trainingPlans = $entityManager->getRepository('\Entity\TrainingPlan')->findBy(array (
			'isSynced' => false,
			'user' => $user->getId(),
		));
		
		$trainingPlansData = array ();
		foreach ($trainingPlans as $trainingPlan) /* @var $trainingPlan \Entity\TrainingPlan */ {
		    $exercisesData = array ();
		    foreach ($trainingPlan->getExercises() as $exercise) /* @var $exercise \Entity\Exercise */ {
		        $exercisesData[] = array (
		        	'ExerciseID' => $exercise->getId(),
		        	'ExerciseName' => $exercise->getName(),
		        	'ExerciseIntensity' => $exercise->getIntensity(),
		        	'ExerciseNote' => $exercise->getNote(),
		        	'GoalDistance' => (double) $exercise->getGoal()->getDistance(),
		        	'GoalDuration' => (double) $exercise->getGoal()->getDuration(),
		        	'GoalIsChallenge' => $exercise->getGoal()->isChallenge(),
		        );
		    }
		    
		    $trainingPlansData[] = array (
		    	'TrainingPlanID' => $trainingPlan->getId(),
		    	'TrainingPlanDateCreated' => $trainingPlan->getDate() != null ? $trainingPlan->getDate()->getTimestamp() : null,
		    	'TrainingPlanName' => $trainingPlan->getName(),
		    	'IsChallenge' => (boolean) $trainingPlan->isChallenge(),
		    	'HasWorkoutGoal' => (boolean) $trainingPlan->hasWorkoutGoal(),
		    	'Note' => (boolean) $trainingPlan->getFeedPost()->getComment(),
		    	'TrainingPlanDeleted' => $trainingPlan->getDeletedTime() != null ? $trainingPlan->getDeletedTime()->getTimestamp() : null,
		    	'SportID' => $trainingPlan->getSport()->getId(),
		    	'Exercises' => $exercisesData,
		    );
		}
		
		if (count($trainingPlansData) > 0) {
			$response = base64_encode(gzencode(\Zend_Json::encode(array (
					'Response' => 'OK',
					'IDsForUploadedData' => array (
							'Sports' => $sportResponse,
							'TrainingPlans' => $trainingPlanResponse,
							'Workouts' => $workoutResponse,
							'Records' => $recordResponse,
					),
					'Data' => array (
							'Sports' => $sportsData,
							'TrainingPlans' => $trainingPlansData,
							'Workouts' => array (),
							'Records' => array (),
					),
			))));
			// 		print_r($response); die();
			return $response;
		}
		
		$workouts = $entityManager->getRepository('\Entity\Workout')->findBy(array (
				'isSynced' => false,
				'user' => $user->getId(),
		), null, 20);
		
		$workoutsData = array ();
		foreach ($workouts as $workout) /* @var $workout \Entity\Workout */ {
			$trainingPlanReportsData = array ();
			foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
			    $exerciseReportsData = array ();
			    foreach ($trainingPlanReport->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
			        $trackPointsData = array ();
			        foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			            $trackPointsData[] = array (
			            	'Time' => $trackPoint->getTime() != null ? $trackPoint->getTime()->getTimestamp() : null,
			            	'Altitude' => (double) $trackPoint->getAlt(),
			            	'Latitude' => (double) $trackPoint->getLat(),
			            	'Longitude' => (double) $trackPoint->getLon(),
			            	'DistanceToLastPoint' => (double) $trackPoint->getDistanceToLastPoint(),
			            	'Speed' => (double) $trackPoint->getSpeed(),
			            	'Heart' => (double) $trackPoint->getHeart(),
			            );
			        }
			        $exerciseReportsData[] = array (
			        	'ExerciseReportID' => $exerciseReport->getId(),
			        	'ExerciseID' => $exerciseReport->getExercise()->getId(),
			        	'StartTime' => $exerciseReport->getStartTime() != null ? $exerciseReport->getStartTime()->getTimestamp() : null,
			        	'EndTime' => $exerciseReport->getEndTime() != null ? $exerciseReport->getEndTime()->getTimestamp() : null,
			        	'Duration' => (int) $exerciseReport->getDuration(),
			        	'Distance' => (double) $exerciseReport->getDistance(),
			        	'TrackPoints' => $trackPointsData,
			        );
			    }
			    $trainingPlanReportsData[] = array (
			    	'TrainingPlanReportID' => $trainingPlanReport->getId(),
			    	'TrainingPlanID' => $trainingPlanReport->getTrainingPlan()->getId(),
			    	'StartTime' => $trainingPlanReport->getStartTime() != null ? $trainingPlanReport->getStartTime()->getTimestamp() : null,
			    	'EndTime' => $trainingPlanReport->getEndTime() != null ? $trainingPlanReport->getEndTime()->getTimestamp() : null,
			    	'Duration' => (int) $trainingPlanReport->getDuration(),
			    	'Distance' => (double) $trainingPlanReport->getDistance(),
			    	'Calories' => (double) $trainingPlanReport->getBurnedCalories(),
			    	'ExerciseReports' => $exerciseReportsData,
			    );
			}
			
		    $workoutsData[] = array (
		    	'WorkoutID' => $workout->getId(),
		    	'FeedID' => $workout->getFeedPost()->getId(),
		    	'WorkoutName' => $workout->getName(),
		    	'StartTime' => $workout->getStartTime() != null ? $workout->getStartTime()->getTimestamp() : null,
		    	'EndTime' => $workout->getEndTime() != null ? $workout->getEndTime()->getTimestamp() : null,
		    	'Duration' => (int) $workout->getDuration(),
		    	'Distance' => (double) $workout->getDistance(),
		    	'IsShared' => (boolean) $workout->isShared(),
		    	'Rating' => (int) $workout->getRating(),
		    	'Playlist' => $workout->getPlayList(),
		    	'Comment' => $workout->getFeedPost() != null ? $workout->getFeedPost()->getComment() : null,
		    	'Facebook' => $workout->getFeedPost() != null ? $workout->getFeedPost()->getSendFacebook() : null,
		    	'Twitter' => $workout->getFeedPost() != null ? $workout->getFeedPost()->getSendTwitter() : null,
		    	'TrainingPlanReports' => $trainingPlanReportsData,
		    );
		}
		
		$records = $entityManager->getRepository('\Entity\Record')->findBy(array (
				'isSynced' => false,
				'user' => $user->getId(),
		));
		
		$recordsData = array ();
		foreach ($records as $record) /* @var $record \Entity\Record */ {
		    $recordsData[] = array (
		    	'RecordID' => $record->getId(),
		    	'WorkoutID' => $record->getWorkout()->getId(),
		    	'Duration' => (int) $record->getDuration(),
		    	'Distance' => (double) $record->getDistance(),
		    	'IsTimeRecord' => (boolean) $record->isTimeRecord(),
		    	'IsMiles' => (boolean) $record->isMiles(),
		    );
		}
		
		$response = base64_encode(gzencode(\Zend_Json::encode(array (
			'Response' => 'OK',
			'IDsForUploadedData' => array (
				'Sports' => $sportResponse,
				'TrainingPlans' => $trainingPlanResponse,
				'Workouts' => $workoutResponse,
				'Records' => $recordResponse,
			),
			'Data' => array (
				'Sports' => $sportsData,
				'TrainingPlans' => $trainingPlansData,
				'Workouts' => $workoutsData,
				'Records' => $recordsData,
			),
		))));
// 		print_r($response); die();
		return $response;
	}
	
	public function synchronizeAcknowledge($userId, $sessionId, $data) {
	    set_time_limit(0);
		$data = utf8_decode(gzdecode(base64_decode(($data))));
	    
	    // 		$userId = 54;
	    // 		$user = $this->userService->getUser(54);
	    
	    $data = \Zend_Json::decode($data);
	    
	    $userService = new User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    if ($data != null) {
	        foreach ($data['TrainingPlans'] as $trainingPlan) {
	            $trainingPlanEntity = $this->workoutService->getTrainingPlan($trainingPlan['TrainingPlanID']);
	            $trainingPlanEntity->setSynced(true);
	            
	            foreach ($trainingPlan['Exercises'] as $exercise) {
	                $exerciseEntity = $this->workoutService->getExercise($exercise['ExerciseID']);
	                $exerciseEntity->setSynced(true);
	                $this->workoutService->getEntityManager()->persist($exerciseEntity);
	            }
	            
	            $this->workoutService->getEntityManager()->persist($trainingPlanEntity);
	        }
	        
// 	        $this->workoutService->refreshTransaction();
	        foreach ($data['Workouts'] as $i => $workout) {
// 	        	print_r($workout);
	        	$workoutEntity = $this->workoutService->getWorkout($workout['WorkoutID']);
	        	$workoutEntity->setSynced(true);
	        	
	        	
	        	foreach ($workout['TrainingPlanReports'] as $trainingPlanReport) {
	        		$trainingPlanReportEntity = $this->workoutService->getTrainingPlanReport($trainingPlanReport['TrainingPlanReportID']);
	        		$trainingPlanReportEntity->setSynced(true);
	        		
	        		foreach ($trainingPlanReport['ExerciseReports'] as $exerciseReport) {
	        			$exerciseReportEntity = $this->workoutService->getExerciseReport($exerciseReport['ExerciseReportID']);
	        			$exerciseReportEntity->setSynced(true);
	        			$this->workoutService->getEntityManager()->persist($exerciseReportEntity);
	        		}
	        		$this->workoutService->persistTrainingPlanReport($trainingPlanReportEntity);
	        	}
	        	$this->workoutService->persistWorkout($workoutEntity);
	        }
	        
	        foreach ($data['Records'] as $i => $record) {
	        	$recordEntity = $this->workoutService->getRecord($record['RecordID']);
	        	$recordEntity->setSynced(true);
	        	$this->workoutService->persistRecord($recordEntity);
	        }
	        
	        $response = \Zend_Json::encode(array (
	        		'Response' => 'OK',
	        ));
	        
	        return $response;
	    } else {
	        throw new \Zend_Exception('No valid data exception', 10);
	    }
	}

	public function getLog()
	{
		$front = \Zend_Controller_Front::getInstance();
		$bootstrap = $front->getParam("bootstrap");
		if (!$bootstrap->hasPluginResource('Log')) {
			return false;
		}
		$log = $bootstrap->getResource('Log');
		return $log;
	}
	
	
	
	public function clearAllSynchronizationData($userId, $sessionId) {
	    $userService = new User();
	    $user = $userService->checkAndUpdateSession($userId, $sessionId);
	    
	    $this->workoutService->clearIsSynced($user);
	    
	    $response = \Zend_Json::encode(array (
	    		'Response' => 'OK',
	    ));
	     
	    return $response;
	}
}