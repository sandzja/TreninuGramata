<?php
namespace Service;


use Service\AbstractService;
class Workout extends AbstractService {
	
	private $userService;
	
	public function init() {
		$this->userService = new User();
	}
	
	/**
	 * Fetch  entity
	 * @param int ID
	 * @return \Entity\Workout
	 */
	public function getWorkout($id) {
		return $this->em->find('\Entity\Workout', (int) $id);
	}
	
	public function removeWorkout(\Entity\Workout $workout) {
		$newsFeed = new \Service\NewsFeed();
		if ($workout->getFeedPost() != null) { 
			$newsFeed->removePost($workout->getFeedPost());
		}
		
		foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
			if ($trainingPlanReport->getChallengeReport() != null) {
				$this->removeChallenge($trainingPlanReport->getChallengeReport());
			}
		}
		if ($workout->getRecord() != null) {
 			$this->removeRecord($workout->getRecord());
		}
		$this->em->remove($workout);
	}
	
	public function removeChallenge($challenge) {
		$this->em->remove($challenge);
	}
	
	public function persistWorkout(\Entity\Workout $workout) {
		$this->em->persist($workout);
		
		return $workout->getId();
	}
	
	public function persistRecord(\Entity\Record $record) {
		$this->em->persist($record);
		
		return $record;
	}
	
	public function persistTrainingPlanReport(\Entity\TrainingPlan\Report $trainingPlanReport) {
		$this->em->persist($trainingPlanReport);
	}
	
	public function saveWorkout(\Service\DTO\Workout $workoutDTO) {
		$sport = $this->getSport($workoutDTO->sportId);
		if ($workoutDTO->userId != null) {
			$user = $this->userService->getUser($workoutDTO->userId);
		} else {
			$user = $this->userService->getCurrentUser();
		}
		
		$workout = new \Entity\Workout();
		$workout->setUser($user);
		$workout->setName($workoutDTO->name);
		if ($workoutDTO->trainingPlanId != null) {
			$trainingPlan = $this->getTrainingPlan($workoutDTO->trainingPlanId);
			$workout->setName($trainingPlan->getName());
		}
		
		$workout->setLocation($workoutDTO->location);
		$workout->setDistance($workoutDTO->distance * $workoutDTO->unit);
		$workout->setDuration($workoutDTO->duration);
		$workout->setRating((int) $workoutDTO->rating);
		
		if ($workoutDTO->startTime == null && $workoutDTO->endTime == null && $workoutDTO->duration != null) {
			$startTime = new \DateTime();
			$endTime = $startTime->setTimestamp($startTime->getTimestamp() - $workoutDTO->duration);
			$workoutDTO->startTime = $startTime;
			$workoutDTO->endTime = $endTime;
		}
		$workout->setStartTime($workoutDTO->startTime);
		$workout->setEndTime($workoutDTO->endTime);
		$workout->setShared(!$workoutDTO->isPrivate);
		if ($workoutDTO->synced) {
		    $workout->setSynced(true);
		}
		$this->em->persist($workout);

		return $workout;
	}
	
	public function saveTrainingPlan(\Service\DTO\TrainingPlan $trainingPlanDTO) {
		$trainingPlan = new \Entity\TrainingPlan();
		$trainingPlan->setSport($this->getSport($trainingPlanDTO->sportId));
		
		if ($trainingPlanDTO->userId == null) {
			$user = $this->userService->getCurrentUser();
		} else {
			$user = $this->userService->getUser($trainingPlanDTO->userId);
		}
		
		$trainingPlan->setUser($user);
		$trainingPlan->setName($trainingPlanDTO->name);
		
		$date = new \DateTime();
		if ($trainingPlanDTO->date != null) {
			$date = $trainingPlanDTO->date;
		}
		$trainingPlan->setDate($date);
		$trainingPlan->setExecutionOrder(1); //FIXME: This datamodel thing is very crap
		$trainingPlan->setDefault((boolean) $trainingPlanDTO->isDefault);
		$trainingPlan->setWorkoutGoal((boolean) $trainingPlanDTO->hasWorkoutGoal);
		$trainingPlan->setChallenge((boolean) $trainingPlanDTO->isChallenge);
		$trainingPlan->setSynced($trainingPlanDTO->synced);
		if ($trainingPlanDTO->deletedTime != null) {
			$trainingPlan->setDeletedTime($trainingPlanDTO->deletedTime);
		}

		foreach ($trainingPlanDTO->exercises as $i => $exerciseDTO) {
			$goal = $this->saveGoal($exerciseDTO->goalDistance, $exerciseDTO->unit, $exerciseDTO->goalDuration, $exerciseDTO->goalIsChallenge, $exerciseDTO->synced);
			
			$exercise = new \Entity\Exercise();
			$exercise->setGoal($goal);
			$exercise->setName($exerciseDTO->name);
			$exercise->setNote($exerciseDTO->note);
			$exercise->setIntensity($exerciseDTO->intensity);
			$exercise->setSynced($exerciseDTO->synced);
			
			$this->em->persist($exercise);
			$trainingPlan->addExercise($exercise);
		}
		
		$this->em->persist($trainingPlan);
		
		return $trainingPlan;
	}
	
	public function saveGoal($distance, $unit, $duration, $isChallenge, $isSynced = false) {
		$goal = new \Entity\Goal();
		if ($unit == null) {
			$unit = 1;
		}
		$goal->setDistance($distance * $unit);
		$goal->setDuration($duration);
		$goal->setChallenge((boolean) $isChallenge);
		$goal->setSynced($isSynced);
		
		$this->em->persist($goal);
		
		return $goal;
	}
	
	/**
	 *
	 * @param unknown_type $workout
	 * @param unknown_type $workoutDTO
	 * @return \Entity\TrainingPlan\Report
	 */
	public function saveTrainingPlanReport(\Entity\Workout $workout, \Service\DTO\Workout $workoutDTO) {
		$trainingPlan = $this->getTrainingPlan($workoutDTO->trainingPlanId);
	
		$trainingPlanReport = new \Entity\TrainingPlan\Report();
		$trainingPlanReport->setDistance($workoutDTO->distance * $workoutDTO->unit);
		$trainingPlanReport->setDuration($workoutDTO->duration);
	
		if ($workoutDTO->startTime == null && $workoutDTO->endTime == null && $workoutDTO->duration != null) {
			$startTime = new \DateTime();
			$endTime = $startTime->setTimestamp($startTime->getTimestamp() - $workoutDTO->duration);
			$workoutDTO->startTime = $startTime;
			$workoutDTO->endTime = $endTime;
		}
		$trainingPlanReport->setStartTime($workoutDTO->startTime);
		$trainingPlanReport->setEndTime($workoutDTO->endTime);
		$trainingPlanReport->setTrainingPlan($trainingPlan);
		$trainingPlanReport->setWorkout($workout);
		$trainingPlanReport->setSynced($workoutDTO->synced);
		$trainingPlanReport->setBurnedCalories($workoutDTO->calories);
		$trainingPlanReport->setPace($workoutDTO->pace);
		$trainingPlanReport->setHeartRate($workoutDTO->heartRate);
		
		if ($workoutDTO->sportId != null) {
			$sport = $this->getSport($workoutDTO->sportId);
		} else {
			$sport = $trainingPlan->getSport();
		}
		
		$trainingPlanReport->setSport($sport);
		$this->em->persist($trainingPlanReport);
	
		return $trainingPlanReport;
	}
	
	/**
	 * Get sport entity
	 * @param int $id
	 * @return \Entity\Sport
	 */
	public function getSport($id) {
		return $this->em->find('\Entity\Sport', (int) $id);
	}
	
	/**
	 * Gets all sports
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUserSports() {
		return $this->em->getRepository('\Entity\Sport')->getUserSports($this->userService->getCurrentUser()->getId());
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTrainingPlansBySport($sportId) {
		return $this->em->getRepository('\Entity\TrainingPlan')->getBySport($sportId, $this->userService->getCurrentUser());
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTrainingPlansByUserAndSport($userId, $sportId) {
		return $this->em->getRepository('\Entity\TrainingPlan')->findBy(array (
			'user' => (int) $userId,
			'deletedTime' => null,
			'sport' => (int) $sportId,
		));
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getWorkoutsByUserAndSport($userId, $sportId) {
		return $this->em->getRepository('\Entity\Workout')->getWorkoutsByUserAndSport($userId, $sportId);
	}
	
	public function getCalendarWorkoutsByUser(\Entity\User $user, \DateTime $date) {
		$workouts = $this->em->getRepository('\Entity\Workout')->getCalendarWorkoutsByUser($user, $date);
		$datedWorkouts = array ();
		foreach ($workouts as $workout) {
			$datedWorkouts[$workout->getStartTime()->format('d')][] = $workout;
		}
		
		return $datedWorkouts;
	}
	
	public function getLastExercise($trainingPlanId) {
		return $this->em->getRepository('\Entity\Exercise')->findOneBy(array (
			'trainingPlan' => (int) $trainingPlanId,
		));
	}
	
	/**
	 * @param int $id
	 * @return \Entity\Exercise
	 */
	public function getExercise($id) {
		return $this->em->find('\Entity\Exercise', (int) $id);
	}
	
	/**
	 * @param int $id
	 * @return \Entity\Exercise\Report
	 */
	public function getExerciseReport($id) {
		return $this->em->find('\Entity\Exercise\Report', (int) $id);
	}
	
	/**
	 * @param int $trainingPlanId
	 * @return \Entity\TrainingPlan
	 */
	public function getTrainingPlan($trainingPlanId) {
		return $this->em->find('\Entity\TrainingPlan', (int) $trainingPlanId);
	}
	
	/**
	 * @deprecated After model change it wont work
	 * @param int $sportId
	 * @param int $trainingPlanId
	 * @return \Entity\Exercise
	 */
	public function getExerciseBySportAndTrainingPlan($sportId, $trainingPlanId) {
		$exercise = $this->em->getRepository('\Entity\Exercise')->findOneBy(array (
			'sport' => (int) $sportId,
			'trainingPlan' => (int) $trainingPlanId,
		));
		
		return $exercise;
	}
	
	public function saveExerciseReport(\Service\DTO\Workout $workoutDTO, \Entity\TrainingPlan\Report $trainingPlanReport) {
		$report = new \Entity\Exercise\Report();
		$report->setTrainingPlanReport($trainingPlanReport);
		$report->setExercise($this->getLastExercise($workoutDTO->trainingPlanId));
		$report->setDistance($trainingPlanReport->getDistance());
		$report->setStartTime($trainingPlanReport->getStartTime());
		$report->setEndTime($trainingPlanReport->getEndTime());
		$report->setSynced($workoutDTO->synced);
		
		if ($trainingPlanReport->getStartTime() != null && $trainingPlanReport->getEndTime() != null) {
			$report->setDuration($trainingPlanReport->getEndTime()->getTimestamp() - $trainingPlanReport->getStartTime()->getTimestamp());
		}
		
		$this->em->persist($report);
		
		return $report;
	}
	
	public function startExerciseReport(\Service\DTO\Workout $workoutDTO, \Entity\TrainingPlan\Report $trainingPlanReport) {
		$report = new \Entity\Exercise\Report();
		$report->setTrainingPlanReport($trainingPlanReport);
		$report->setExercise($this->getExercise($workoutDTO->exerciseId));
		$report->setStartTime($workoutDTO->startTime);
		$report->setSynced($workoutDTO->synced);
		
		$this->em->persist($report);
		
		return $report;
	}
	
	public function saveTrackPointsByCoordinates(\Service\DTO\Workout $workoutDTO, \Entity\Exercise\Report $report) {
		foreach ($workoutDTO->trackPoints as $trackPointCoordinates) {
			$trackPoint = new \Entity\Exercise\TrackPoint();
			$trackPoint->setReport($report);
			$trackPoint->setIsUploaded(true);
			
			list ($lat, $lon) = explode(';', $trackPointCoordinates);
			$trackPoint->setLat($lat);
			$trackPoint->setLon($lon);
			$trackPoint->setTime(new \DateTime());
			
			$this->em->persist($trackPoint);
		}
	}
	
	/**
	 * @param int $reportId
	 * @return \Entity\Exercise\Report
	 */
	public function getReportBy($reportId) {
		return $this->em->getRepository('\Entity\Exercise\Report')->find($reportId);
	}
	
	/**
	 * @param int $workoutId
	 * @return \Entity\Exercise\Report
	 */
	public function getCurrentlyActiveReport($workoutId) {
		return $this->em->getRepository('\Entity\Exercise\Report')->getCurrentlyActiveReport($workoutId);
	}
	
	/**
	 * @param int $userId
	 * @param int $sportId
	 * @return \Entity\Record
	 */
	public function getRecordBySport($sportId, $userId = null) {
		return $this->em->getRepository('\Entity\Record')->getRecord($this->getSport($sportId), $this->userService->getUser($userId));
	}
	
	public function getUserRecordsByCriteria(array $record, \Entity\User $user, $sport = null) {
		return $this->em->getRepository('\Entity\Record')->getUserRecordsByCriteria($record, $user, $sport);
	}
	
	/**
	 * @param int $userId
	 * @param int $sportId
	 * @return \Entity\Record
	 */
	public function getRecordBySportByFriendName($sportId, $friendName) {
		return $this->em->getRepository('\Entity\Record')->getRecordByFriendName($this->getSport($sportId), $friendName);
	}
	
	public function getRecordPosition(\Entity\Record $record) {
	    $records = $this->em->getRepository('\Entity\Record')->getRecordPositions($record);
	    
	    return count($records);
	}
	
	public function getTrackPointsByTimestamp($reportId, $timestamp) {
		return $this->em->getRepository('\Entity\Exercise\TrackPoint')->getByTimestamp($reportId, $timestamp);
	}

	public function getActiveTrackPoints() {
		return $this->em->getRepository('\Entity\Exercise\TrackPoint')->getActives();
	}

	public function calculateDuration($hours, $minutes, $seconds) {
		return ((int) $hours * 60 * 60) + ((int) $minutes * 60) + (int) $seconds;
	}


	public function getGoaledWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\Workout')->getGoaledWorkouts($user, $limit, $offset);
	}
	
	public function getCalendarGoaledWorkouts(\Entity\User $user, \DateTime $date) {
		$workouts = $this->em->getRepository('\Entity\Workout')->getCalendarGoaledWorkouts($user, $date);
		
		$datedWorkouts = array ();
		foreach ($workouts as $workout) {
			$datedWorkouts[$workout->getStartTime()->format('d')][] = $workout;
		}
		
		return $datedWorkouts;
	}
	
	public function getChallengedWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\Workout')->getChallengedWorkouts($user, $limit, $offset);
	}
	
	public function getCalendarChallengedWorkouts(\Entity\User $user, \DateTime $date) {
		$workouts = $this->em->getRepository('\Entity\Workout')->getCalendarChallengedWorkouts($user, $date);
		
		$datedWorkouts = array ();
		foreach ($workouts as $workout) {
			$datedWorkouts[$workout->getStartTime()->format('d')][] = $workout;
		}
		
		return $datedWorkouts;
	}
	
	public function getRecordedWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\Workout')->getRecordedWorkouts($user, $limit, $offset);
	}
	
	public function getCalendarRecordedWorkouts(\Entity\User $user, \DateTime $date) {
		$workouts = $this->em->getRepository('\Entity\Workout')->getCalendarRecordedWorkouts($user, $date);
		
		$datedWorkouts = array ();
		foreach ($workouts as $workout) {
			$datedWorkouts[$workout->getStartTime()->format('d')][] = $workout;
		}
		
		return $datedWorkouts;
	}
	
	public function getDailyWorkoutGraphs(\Entity\User $user, $endTime, $sportId) {
		if ($endTime == null) {
			$endTime = new \DateTime();
		} else {
			$endTime = \DateTime::createFromFormat('Y-m-d', $endTime);
		}
		
		$times = $this->em->getRepository('\Entity\Workout')->getDailyWorkoutGraphs($user, $endTime, $sportId);
		$maxTime = 0.1;
		foreach ($times as $time) {
			if ($maxTime < $time['seconds']) {
				$maxTime = $time['seconds'];
			}
		}
		
		$restructuredTimes = array ();
		foreach ($times as $time) {
			$restructuredTimes[$time['date']]['time'] = $time['time'];
			$restructuredTimes[$time['date']]['percent'] = round($time['seconds'] / $maxTime * 100);
		}

		return $restructuredTimes;
	}
	
	public function getWeeklyWorkoutGraphs(\Entity\User $user, $endTime, $sportId) {
		if ($endTime == null) {
			$endTime = new \DateTime();
		} else {
			$endTime = \DateTime::createFromFormat('Y-m-d', $endTime);
		}
	
		$times = $this->em->getRepository('\Entity\Workout')->getWeeklyWorkoutGraphs($user, $endTime, $sportId);
		$maxTime = 0.1;
		foreach ($times as $time) {
			if ($maxTime < $time['seconds']) {
				$maxTime = $time['seconds'];
			}
		}
	
		$restructuredTimes = array ();
		foreach ($times as $time) {
			$restructuredTimes[$time['week']]['time'] = $time['time'];
			$restructuredTimes[$time['week']]['percent'] = round($time['seconds'] / $maxTime * 100);
		}
	
		return $restructuredTimes;
	}
	
	public function getMonthlyWorkoutGraphs(\Entity\User $user, $endTime, $sportId) {
		if ($endTime == null) {
			$endTime = new \DateTime();
		} else {
			$endTime = \DateTime::createFromFormat('Y-m-d', $endTime);
		}
	
		$times = $this->em->getRepository('\Entity\Workout')->getMonthlyWorkoutGraphs($user, $endTime, $sportId);
		$maxTime = 0.1;
		foreach ($times as $time) {
			if ($maxTime < $time['seconds']) {
				$maxTime = $time['seconds'];
			}
		}
	
		$restructuredTimes = array ();
		foreach ($times as $time) {
			$restructuredTimes[$time['year'] . '-' . $time['month']]['time'] = $time['time'];
			$restructuredTimes[$time['year'] . '-' . $time['month']]['percent'] = round($time['seconds'] / $maxTime * 100);
		}
		return $restructuredTimes;
	}
	
	/**
	 * Gets user trainingplans
	 * @param \Entity\User $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTrainingPlans($userId = null, $visibility = null, $limit = 10, $offset = 0) {
		if ($userId == null) {
			$user = $this->userService->getCurrentUser();
		} else {
			$user = $this->userService->getUser($userId);
		}
		
		$trainingPlans = array ();
		
		if ($visibility == 'database') {
			$trainingPlans = $this->getDatabaseTrainingPlans($limit, $offset);
		} else if ($visibility == 'friends') {
			$trainingPlans = $this->getFriendsTrainingPlans($user, $limit, $offset);
		} else {
			$trainingPlans = $this->getUserTrainingPlans($user, $limit, $offset);
		}
		
		return $trainingPlans;
	}
	
	public function getFeaturedTrainingPlans() {
		return $this->em->getRepository('\Entity\TrainingPlan')->findBy(array (
				'isFeatured' => true,
		));
	}
	
	/**
	 * Gets user trainingplans
	 * @param \Entity\User $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function searchTrainingPlans($type, $name, $sportId = null, $intensity = null, $userId = null, $visibility = null, $limit = null, $offset = null) {
		if ($userId == null) {
			$user = $this->userService->getCurrentUser();
		} else {
			$user = $this->userService->getUser($userId);
		}
	
		$trainingPlans = array ();
		
		if ($type == 'database') {
			$trainingPlans = $this->em->getRepository('\Entity\TrainingPlan')->searchDatabaseTrainingPlans($name, $sportId, $intensity, $user, $limit, $offset);
		} else if ($type == 'friends') {
			$trainingPlans = $this->em->getRepository('\Entity\TrainingPlan')->searchFriendsTrainingPlans($name, $sportId, $intensity, $user, $limit, $offset);
		} else {
			$trainingPlans = $this->em->getRepository('\Entity\TrainingPlan')->searchUserTrainingPlans($name, $sportId, $intensity, $user, $limit, $offset);
		}
		
	
		return $trainingPlans;
	}

	/**
	 * SV Gets user trainingplans sets
	 * @return Array
	 */
   	public function searchTrainingPlansSets($nr = 1, $sets = null, $coach = null, $sportId = null, $intensity = null, $event = null, $name = null, $limit = null, $offset = null) {
   		$trainingPlans = array();

		$trainingPlans = $this->em->getRepository('\Entity\SetSets')->searchTrainingPlans($nr, $sets, $coach, $sportId, $intensity, $event, $name, $limit, $offset);

   		return $trainingPlans;
   	}
    /* SV end */
	
	public function getUserTrainingPlans(\Entity\User $user, $limit, $offset) {
		return $this->em->getRepository('\Entity\TrainingPlan')->getUserTrainingPlans($user, $limit, $offset);
	}
	
	public function getFriendsTrainingPlans(\Entity\User $user, $limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\TrainingPlan')->getFriendsTrainingPlans($user, $limit, $offset);
	}
	
	public function getDatabaseTrainingPlans($limit = null, $offset = null) {
		return $this->em->getRepository('\Entity\TrainingPlan')->getDatabaseTrainingPlans($limit, $offset);
	}
	
	/**
	 * Gets trainingplan report
	 * @param integer $id
	 * @return \Entity\TrainingPlan\Report
	 */
	public function getTrainingPlanReport($id) {
		return $this->em->find('\Entity\TrainingPlan\Report', (int) $id);
	}

	public function getIntensityGraph(\Entity\TrainingPlan $trainingPlan) {
		$intensityDistances = array ();
		foreach ($trainingPlan->getExercises() as $i => $exercise) /* @var $exercise \Entity\Exercise */ {
			if ($exercise->getGoal()->getDistance() != 0) {
				$intensityDistances[] = $exercise->getGoal()->getDistance();
			} else if ($exercise->getGoal()->getDuration() != 0) {
				$intensityDistances[] = $trainingPlan->getSport()->getIntensitySpeed() * $exercise->getGoal()->getDuration();
			}
		}
		
		$percents = array ();
		$max = array_sum($intensityDistances);
		if ($max > 0) {
			$sum = 0;
			foreach ($intensityDistances as $i => $distance) {
				$percent = round($distance / $max * 100, 0);
				if (count($intensityDistances) - 1 != $i) {
					$percents[] = $percent;
				} else {
					$percents[] = 100 - $sum;
				}
				$sum += $percent;
			}
		}
		
		return $percents;
	}

	public function addToMyPlans($trainingPlanId) {
		
		$originalTrainingPlan = $this->getTrainingPlan($trainingPlanId);
		$originalPost = $originalTrainingPlan->getFeedPost();
		
		$myTrainingPlan = clone $originalTrainingPlan;
		$myPost = new \Entity\Feed\Post\TrainingPlan();
		$myPost->setAuthor($this->userService->getCurrentUser());
		$myPost->setComment($originalPost->getComment());
		$myPost->setIsPrivate($originalPost->isPrivate());
		$myPost->setTrainingPlan($myTrainingPlan);
		
		$myTrainingPlan->setUser($this->userService->getCurrentUser());
		$myTrainingPlan->setOriginalTrainingPlan($this->getFirstOriginalTrainingPlan($originalTrainingPlan));
		$myTrainingPlan->setSynced(false);
		
		foreach ($originalTrainingPlan->getExercises() as $exercise) {
			$newExercise = clone $exercise;
			$myTrainingPlan->addExercise($newExercise);
			$this->em->persist($newExercise);
		}
		
		$this->em->persist($myPost);
		$this->em->persist($myTrainingPlan);
		
	}
	
	public function getFirstOriginalTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		if ($trainingPlan->getOriginalTrainingPlan() == null) {
			return $trainingPlan;
		} else {
			return $this->getFirstOriginalTrainingPlan($trainingPlan->getOriginalTrainingPlan());
		}
	}

	public function getRecord($id) {
		return $this->em->find('\Entity\Record', $id);
	}
	
	public function removeRecord(\Entity\Record $record) {
		$this->em->remove($record);
	}

	
	/**
	 * @param int $id
	 * @return \Entity\Challenge
	 */
	public function getChallenge($id) {
		return $this->em->find('\Entity\Challenge', (int) $id);
	}
	
	/**
	 * @param \Service\DTO\Challenge $challengeDTO
	 * @return \Entity\Challenge
	 */
	public function saveChallenge(\Service\DTO\Challenge $challengeDTO) {
		$challenge = new \Entity\Challenge();
		if ($challengeDTO->challengeId != null) {
			$challenge = $this->getChallenge($challengeDTO->challengeId);
		}
		
		$challenge->setDate($challengeDTO->date);
		$challenge->setOpponentUser($this->userService->getUser($challengeDTO->opponentUserId));
		$challenge->setRecord($this->getRecord($challengeDTO->recordId));
		$challenge->setTrainingPlan($this->getTrainingPlan($challengeDTO->trainingPlanId));
		$challenge->setUser($this->userService->getUser($challengeDTO->userId));
		$challenge->setWorkout($this->getWorkout($challengeDTO->workoutId));
		
		$this->em->persist($challenge);
		
		return $challenge;
	}
	
	/**
	 * @param \Service\DTO\Challenge $challengeDTO
	 * @return \Entity\ChallengeReport
	 */
	public function saveChallengeReport(\Service\DTO\Challenge $challengeDTO) {
		$challenge = $this->getChallenge($challengeDTO->challengeId);
		
		$challengeReport = new \Entity\Challenge\Report();
		$challengeReport->setChallenge($challenge);
		$challengeReport->setWinChallenge((boolean) $challengeDTO->didWinChallenge);
		$challengeReport->setTrainingPlanReport($this->getTrainingPlanReport($challengeDTO->trainingPlanReportId));
		
		$this->em->persist($challengeReport);
		
		return $challengeReport;
	}

	public function clearIsSynced(\Entity\User $user) {
	    foreach ($user->getRecords() as $record) /* @var $record \Entity\Record */ {
	        $record->setSynced(false);
	        $this->em->persist($record);
	    }
	    
	    foreach ($user->getSports() as $sport) /* @var $sport \Entity\Sport */ {
	        $sport->setSynced(false);
	    	$this->em->persist($sport);
	    }
	    
	    foreach ($user->getWorkouts() as $workout) /* @var $workout \Entity\Workout */ {
	    	$workout->setSynced(false);
	    	$this->em->persist($workout);
	    }
	    
	    foreach ($user->getTrainingPlans() as $trainingPlan) /* @var $trainingPlan \Entity\TrainingPlan */ {
	        $trainingPlan->setSynced(false);
	        $this->em->persist($trainingPlan);
	        
	        foreach ($trainingPlan->getExercises() as $exercise) /* @var $exercise \Entity\Exercise */ {
	            $exercise->setSynced(false);
	            $this->em->persist($exercise);
	            
	            $exerciseGoal = $exercise->getGoal();
	            if ($exerciseGoal != null) {
	                $exerciseGoal->setSynced(false);
	                $this->em->persist($exerciseGoal);
	            }
	        }
	        
	        foreach ($trainingPlan->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
	            $trainingPlanReport->setSynced(false);
	            $this->em->persist($trainingPlanReport);
	            
	            foreach ($trainingPlanReport->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
	                $exerciseReport->setSynced(false);
	                $this->em->persist($exerciseReport);
	            }
	        }
	    }
	}
	
	public function deleteTrainingPlan($id) {
		$trainingPlan = $this->getTrainingPlan($id);
		if ($trainingPlan->getUser() == $this->userService->getCurrentUser()) {
			$trainingPlan->setDeletedTime(new \DateTime());
		
			$this->em->persist($trainingPlan);
		}
	}
	
	public function createDefaultTrainingData(\Entity\User $user) {
		
		$trainingPlans = array (
			array (
				'sportId' => 1,
				'trainingPlans' => 	array (
					array (
						'trainingPlanName' => 'Basic tracking',
						'exercises' => array (
							array (
								'exerciseName' => 'Tracking',
								'goalDistance' => null,
								'goalDuration' => null,
								'intensity' => \Entity\Exercise::INTENSITY_NONE,
								'note' => 'Try planned workout for better results',
							)
						),
					),
					array (
						'trainingPlanName' => '40min Easy',
						'exercises' => array (
							array (
								'exerciseName' => 'Warm up',
								'goalDistance' => null,
								'goalDuration' => 5*60,
								'intensity' => \Entity\Exercise::INTENSITY_WC,
								'note' => 'Warm up',
							),
							array (
								'exerciseName' => 'Work phase',
								'goalDistance' => null,
								'goalDuration' => 30*60,
								'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
								'note' => 'Relax and run easy',
							),
// 							array (
// 								'exerciseName' => 'Rest phase',
// 								'goalDistance' => null,
// 								'goalDuration' => null,
// 								'intensity' => \Entity\Exercise::INTENSITY_NONE,
// 								'note' => '',
// 							),
							array (
								'exerciseName' => 'Cool down',
								'goalDistance' => null,
								'goalDuration' => 5*60,
								'intensity' => \Entity\Exercise::INTENSITY_WC,
								'note' => 'Cool down',
							),
						),
					),
					array (
						'trainingPlanName' => '65min Medium',
						'exercises' => array (
							array (
									'exerciseName' => 'Warm up',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_WC,
									'note' => 'Warm up',
							),
							array (
									'exerciseName' => 'Rest phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_LOW,
									'note' => 'Prepare for the work phase',
							),
							array (
									'exerciseName' => 'Work phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
									'note' => 'Run near the top of your Medium pace',
							),
							array (
									'exerciseName' => 'Rest phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_LOW,
									'note' => 'Recovery 5min',
							),
							array (
									'exerciseName' => 'Work phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
									'note' => 'Run near the top of your Medium pace',
							),
							array (
									'exerciseName' => 'Rest phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_LOW,
									'note' => 'Recovery 5min',
							),
							array (
									'exerciseName' => 'Work phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
									'note' => 'Run near the top of your Medium pace',
							),
							array (
									'exerciseName' => 'Rest phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_LOW,
									'note' => 'Recovery 5min',
							),
							array (
									'exerciseName' => 'Work phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
									'note' => 'Run near the top of your Medium pace',
							),
							array (
									'exerciseName' => 'Rest phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_LOW,
									'note' => 'Recovery 5min',
							),
							array (
									'exerciseName' => 'Work phase',
									'goalDistance' => null,
									'goalDuration' => 5*60,
									'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
									'note' => 'Run near the top of your Medium pace',
							),
							array (
									'exerciseName' => 'Cool down',
									'goalDistance' => null,
									'goalDuration' => 10*60,
									'intensity' => \Entity\Exercise::INTENSITY_WC,
									'note' => 'Cool down',
							),
						),
					),
				),
			),
				array (
						'sportId' => 2,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
								array (
										'trainingPlanName' => '60min Easy',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 40*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Relax and go easy',
												),
// 												array (
// 														'exerciseName' => 'Rest phase',
// 														'goalDistance' => null,
// 														'goalDuration' => null,
// 														'intensity' => \Entity\Exercise::INTENSITY_NONE,
// 														'note' => '',
// 												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
								array (
										'trainingPlanName' => '95min Medium',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Prepare for the work phase',
														
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
						),
				),
				array (
						'sportId' => 3,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
								array (
										'trainingPlanName' => '40min Easy',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 30*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Relax and go easy',
												),
// 												array (
// 														'exerciseName' => 'Rest phase',
// 														'goalDistance' => null,
// 														'goalDuration' => null,
// 														'intensity' => \Entity\Exercise::INTENSITY_NONE,
// 														'note' => '',
// 												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
								array (
										'trainingPlanName' => '65min Medium',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Prepare for the work phase',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Walk near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Walk near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Walk near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Walk near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Walk near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
						),
				),
				array (
						'sportId' => 4,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
								array (
										'trainingPlanName' => '60min Easy',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 40*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Relax and go easy',
												),
// 												array (
// 														'exerciseName' => 'Rest phase',
// 														'goalDistance' => null,
// 														'goalDuration' => null,
// 														'intensity' => \Entity\Exercise::INTENSITY_NONE,
// 												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
								array (
										'trainingPlanName' => '95min Medium',
										'exercises' => array (
												array (
														'exerciseName' => 'Warm up',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Warm up',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Prepare for the work phase',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
														
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Rest phase',
														'goalDistance' => null,
														'goalDuration' => 5*60,
														'intensity' => \Entity\Exercise::INTENSITY_LOW,
														'note' => 'Recovery 5min',
												),
												array (
														'exerciseName' => 'Work phase',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_MEDIUM,
														'note' => 'Bike near the top of your Medium pace',
												),
												array (
														'exerciseName' => 'Cool down',
														'goalDistance' => null,
														'goalDuration' => 10*60,
														'intensity' => \Entity\Exercise::INTENSITY_WC,
														'note' => 'Cool down',
												),
										),
								),
						),
				),
			array (
				'sportId' => 5,
				'trainingPlans' => 	array (
					array (
						'trainingPlanName' => 'Basic tracking',
						'exercises' => array (
							array (
								'exerciseName' => 'Tracking',
								'goalDistance' => null,
								'goalDuration' => null,
								'intensity' => \Entity\Exercise::INTENSITY_NONE,
								'note' => 'Try planned workout for better results',
							)
						),
					),
				),
			),
				array (
						'sportId' => 6,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 7,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 8,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 9,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 10,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 11,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 12,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 13,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 14,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 15,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 16,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 17,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 18,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 19,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 20,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 21,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 22,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 23,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 24,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 25,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 26,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 27,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 28,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 29,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 30,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 31,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 32,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 33,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 34,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 35,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 36,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 37,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 38,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 39,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
				array (
						'sportId' => 40,
						'trainingPlans' => 	array (
								array (
										'trainingPlanName' => 'Basic tracking',
										'exercises' => array (
												array (
														'exerciseName' => 'Tracking',
														'goalDistance' => null,
														'goalDuration' => null,
														'intensity' => \Entity\Exercise::INTENSITY_NONE,
														'note' => 'Try planned workout for better results',
												)
										),
								),
						),
				),
		);
		
	    foreach ($trainingPlans as $sport) {
	        foreach ($sport['trainingPlans'] as $trainingPlan) {
	            $trainingPlanEntity = new \Entity\TrainingPlan();
	            $trainingPlanEntity->setChallenge(false);
	            $trainingPlanEntity->setDate(new \DateTime());
	            $trainingPlanEntity->setDefault(false);
	            $trainingPlanEntity->setDeletedTime(null);
	            $trainingPlanEntity->setExecutionOrder(1);
	            $trainingPlanEntity->setSport($this->getSport($sport['sportId']));
	            $trainingPlanEntity->setName($trainingPlan['trainingPlanName']);
	            $trainingPlanEntity->setUser($user);
	            $trainingPlanEntity->setWorkoutGoal(false);
	            
	            $this->em->persist($trainingPlanEntity);
	            
	            $feedPost = new \Entity\Feed\Post\TrainingPlan();
	            $feedPost->setAuthor($user);
	            $feedPost->setComment('');
	            $feedPost->setIsPrivate(true);
	            $feedPost->setTrainingPlan($trainingPlanEntity);
	            
	            $this->em->persist($feedPost);
	            
	            foreach ($trainingPlan['exercises'] as $attributes) {
	                $goal = new \Entity\Goal();
	                $goal->setDuration($attributes['goalDuration']);
	                $goal->setDistance($attributes['goalDistance']);
	                $this->em->persist($goal);
	                
	                $exercise = new \Entity\Exercise();
	                $exercise->setIntensity($attributes['intensity']);
	                $exercise->setName($attributes['exerciseName']);
	                $exercise->setNote(isset($attributes['note']) ? $attributes['note'] : '');
	                $exercise->setGoal($goal);
	                $exercise->setTrainingPlan($trainingPlanEntity);
	                $this->em->persist($exercise);
	                
	            }
	        }
	    }
	}
	
	public function importGpx($fileName) {
		$xml = new \SimpleXMLElement(file_get_contents($fileName));
		
		$gpxData = array ();
		foreach ($xml as $element) {
			if ($element->getName() != 'wpt') {
				continue;
			}
			$lat = $element->attributes()->lat;
			$lon = $element->attributes()->lon;
			$time = new \DateTime($element->time);
			
			$data = new \stdClass();
			$data->latitude = (double) $lat;
			$data->longitude = (double) $lon;
			$data->time = $time;
			
			$gpxData[] = $data;
			
		}
	
		@unlink($fileName);
		
		return $gpxData;
	}
	
	public function getAllStaticRecords() {
		return array (
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 1 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 3 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 5 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 10 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 20 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 30 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 40 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 50 * 1000,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 100 * 1000,
						'isInMiles' => false,
				),
				
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 1 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 3 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 5 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 10 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 20 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 30 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 40 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 50 * 1609.34,
						'isInMiles' => true,
				),
				array (
						'isTimeRecord' => false,
						'duration' => null,
						'distance' => 100 * 1609.34,
						'isInMiles' => true,
				),
				
				array (
					'name' => 'Half-Marathon',
					'isTimeRecord' => false,
					'duration' => null,
					'distance' => 21097.5,
					'isInMiles' => false,
				),
				array (
					'name' => 'Marathon',
					'isTimeRecord' => false,
					'duration' => null,
					'distance' => 42195.0,
					'isInMiles' => false,
				),
				
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 0.5,
						'distance' => null,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 1,
						'distance' => null,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 2,
						'distance' => null,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 3,
						'distance' => null,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 4,
						'distance' => null,
						'isInMiles' => false,
				),
				array (
						'isTimeRecord' => true,
						'duration' => 60 * 5,
						'distance' => null,
						'isInMiles' => false,
				),
		);
	}

}


