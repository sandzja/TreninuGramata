<?php
namespace Service\Api2;

use Service\ExerciseReport;

use Entity\Exercise\Report;
use Service\Exercise;
use Service\Workout;

class Exercise {
	
	public function reportStarted($ExerciseReportStarted) {
		$userService = new User();
		$userService->checkAndUpdateSession(@$ExerciseReportStarted['UserID'], @$ExerciseReportStarted['SessionID']);

		$workoutService = new Workout();
		$exerciseService = new Exercise();
		
		$workout = $workoutService->fetch(@$ExerciseReportStarted['WorkoutID']);
		$exercise = $exerciseService->fetch(@$ExerciseReportStarted['ExerciseID']);
		
		
		$exerciseReport = new Report();
		$startTime = new \DateTime();
		$startTime->setTimestamp(@$ExerciseReportStarted[]);
		$exerciseReport->setStartTime($startTime['ExerciseReportStartTime']);
		$exerciseReport->setWorkout($workout);
		$exerciseReport->setExercise($exercise);
		$exerciseReport->setSynced(true);
		
		$exerciseService->persistReport($exerciseReport);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
			'ExerciseReportID' => $exerciseReport->getId(),
			'TrackID' => $exerciseReport->getTrack()->getId(),
		));
		
		return $response;
	}
	
}