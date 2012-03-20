<?php
namespace Entity\TrainingPlan;

use Doctrine\Common\Collections\ArrayCollection;

use Entity\AbstractEntity;

/**
 * @Table(name="TrainingPlanReport")
 * @Entity
 */
class Report extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @Column(name="burned_calories", type="integer")
	 * @var integer
	 */
	protected $burnedCalories;
	
	/**
	 * @Column(name="heart", type="integer")
	 * @var integer
	 */
	protected $heartRate;
	
	/**
	 * @Column(name="pace", type="integer")
	 * @var integer
	 */
	protected $pace;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $distance;
	
	/**
	 * @Column(type="integer")
	 * @var double
	 */
	protected $duration;
	
	/**
	 * @Column(name="start_time", type="datetime")
	 * @var DateTime
	 */
	protected $startTime;
	
	/**
	 * @Column(name="end_time", type="datetime")
	 * @var DateTime
	 */
	protected $endTime;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\TrainingPlan", inversedBy="trainingPlanReports")
	 * @JoinColumn(name="training_plan_id")
	 * @var TrainingPlan
	 */
	protected $trainingPlan;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Workout", inversedBy="trainingPlanReports", cascade={"remove"})
	 * @JoinColumn(name="workout_id")
	 * @var Workout
	 */
	protected $workout;
	
	/**
	 * @OneToMany(targetEntity="\Entity\Exercise\Report", mappedBy="trainingPlanReport", cascade={"persist"})
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $exerciseReports;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Sport", inversedBy="trainingPlanReports")
	 * @JoinColumn(name="sport_id")
	 * @var \Entity\Sport
	 */
	protected $sport;
	
	/**
	 * @OneToOne(targetEntity="\Entity\Challenge\Report", mappedBy="trainingPlanReport", cascade={"remove"})
	 * @var \Entity\Challenge\Report
	 */
	protected $challengeReport;
	
	public function __construct() {
		$this->exerciseReports = new ArrayCollection();
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return integer the $burnedCalories
	 */
	public function getBurnedCalories() {
		return $this->burnedCalories;
	}

	/**
	 * @param integer $name
	 */
	public function setBurnedCalories($burnedCalories) {
		$this->burnedCalories = $burnedCalories;
	}

	/**
	 * @return number $heartRate
	 */
	public function getHeartRate() {
		return $this->heartRate;
	}

	/**
	 * @param number $heartRate
	 */
	public function setHeartRate($heartRate) {
		$this->heartRate = $heartRate;
	}

	/**
	 * @return number $pace
	 */
	public function getPace() {
		return $this->pace;
	}

	/**
	 * @param number $pace
	 */
	public function setPace($pace) {
		$this->pace = $pace;
	}

	/**
	 * @return double the $distance
	 */
	public function getDistance() {
		return $this->distance;
	}

	/**
	 * @param double $distance
	 */
	public function setDistance($distance) {
		$this->distance = $distance;
	}
	
	
	/**
	 * @return int the $duration
	 */
	public function getDuration() {
		return $this->duration;
	}
	
	/**
	 * @param int $duration
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * @return \DateTime the $startTime
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/**
	 * @param DateTime $startTime
	 */
	public function setStartTime(\DateTime $startTime = null) {
		$this->startTime = $startTime;
	}

	/**
	 * @return \DateTime the $endTime
	 */
	public function getEndTime() {
		return $this->endTime;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime(\DateTime $endTime = null) {
		$this->endTime = $endTime;
	}

	/**
	 * @return boolean $isSynced
	 */
	public function isSynced() {
		return $this->isSynced;
	}
	
	
	/**
	 * @param boolean $isSynced
	 */
	public function setSynced($isSynced) {
		$this->isSynced = $isSynced;
	}
	
	/**
	 * @return \Entity\TrainingPlan the $trainingPlan
	 */
	public function getTrainingPlan() {
		return $this->trainingPlan;
	}

	/**
	 * @param TrainingPlan $trainingPlan
	 */
	public function setTrainingPlan($trainingPlan) {
		$this->trainingPlan = $trainingPlan;
	}

	/**
	 * @return \Entity\Workout $workout
	 */
	public function getWorkout() {
		return $this->workout;
	}

	/**
	 * @param \Entity\Workout $workout
	 */
	public function setWorkout(\Entity\Workout $workout) {
		$this->workout = $workout;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $exerciseReports
	 */
	public function getExerciseReports() {
		return $this->exerciseReports;
	}

	
	/**
	 * @param \Entity\Exercise\Report $exerciseReport
	 */
	public function addExerciseReport(\Entity\Exercise\Report $exerciseReport) {
		$this->exerciseReports[] = $exerciseReport;
		$exerciseReport->setTrainingPlanReport($this);
	}

	public function getAverageSpeed() {
		$speed = 0;
		$trackPointsCount = 0;
		foreach ($this->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
			foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
				$speed += $trackPoint->getSpeed();
				$trackPointsCount++;
			}
		}
	
		if ($trackPointsCount == 0) {
			return 0;
		}
		
		return round($speed / $trackPointsCount, 2);
	}
	
	public function getHighestSpeed() {
		$speed = 0;
		$trackPointsCount = 0;
		foreach ($this->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
			foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
				if ($speed < $trackPoint->getSpeed()) {
					$speed = $trackPoint->getSpeed();
					$trackPointsCount++;
				}
			}
		}
	
		return $speed;
	}
	
	public function getAveragePace() {
		if ($this->duration == 0 || $this->distance == 0) {
			
			return 0;
		}
		
		$pace = ($this->duration / 60) / ($this->distance / 1000);
	
		return round($pace, 2);
	}
	
// 	public function getHighestPace() {
// 		$pace = 0;
// 		$trackPointsCount = 0;
// 		foreach ($this->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
// 			foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
// 				if ($pace < $trackPoint->getPulse()) {
// 					$pace = $trackPoint->getPulse();
// 					$trackPointsCount++;
// 				}
// 			}
// 		}
	
// 		return $pace;
// 	}
	
	public function getAverageHeartRate() {
		$heart = 0;
		$trackPointsCount = 0;
		foreach ($this->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
			foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
				$heart += $trackPoint->getHeart();
				$trackPointsCount++;
			}
		}
	
		if ($trackPointsCount == 0) {
			return 0;
		}
		
		return round($heart / $trackPointsCount, 2);
	}
	
	public function getHighestHeartRate() {
		$heart = 0;
		$trackPointsCount = 0;
		foreach ($this->getExerciseReports() as $exerciseReport) /* @var $exerciseReport \Entity\Exercise\Report */ {
			foreach ($exerciseReport->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
				if ($heart < $trackPoint->getHeart()) {
					$heart = $trackPoint->getHeart();
					$trackPointsCount++;
				}
			}
		}
	
		return $heart;
	}
	
	/**
	 * @return \Entity\Sport $sport
	 */
	public function getSport() {
		return $this->sport;
	}

	/**
	 * @param \Entity\Sport $sport
	 */
	public function setSport(\Entity\Sport $sport) {
		$this->sport = $sport;
	}
	
	/**
	 * @return \Entity\Challenge\Report $challengeReport
	 */
	public function getChallengeReport() {
		return $this->challengeReport;
	}

	/**
	 * @param \Entity\Challenge\Report $challengeReport
	 */
	public function setChallengeReport(\Entity\Challenge\Report $challengeReport) {
		$this->challengeReport = $challengeReport;
	}



}