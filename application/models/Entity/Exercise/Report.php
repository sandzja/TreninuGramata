<?php

namespace Entity\Exercise;

use Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Table(name="ExerciseReport")
 * @Entity(repositoryClass="\Repository\Report")
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
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $distance;
	
	/**
	 * @Column(type="decimal")
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
	 * @ManyToOne(targetEntity="\Entity\TrainingPlan\Report", inversedBy="exerciseReports")
	 * @JoinColumn(name="training_plan_report_id")
	 * @var \Entity\TrainingPlan\Report
	 */
	protected $trainingPlanReport;
	
	/**
	 * @OneToOne(targetEntity="\Entity\Exercise")
	 * @JoinColumn(name="exercise_id")
	 * @var Exercise;
	 */
	protected $exercise;
	
	/**
	 * @OneToMany(targetEntity="Entity\Exercise\TrackPoint", mappedBy="report", cascade={"persist"})
	 * @var ArrayCollection
	 */
	protected $trackPoints;
	
	public function __construct() {
		$this->trackPoints = new ArrayCollection();
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
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
	 * @return double the $duration
	 */
	public function getDuration() {
		return $this->duration;
	}
	
	/**
	 * @param double $duration
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * @return DateTime the $startTime
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/**
	 * @param DateTime $startTime
	 */
	public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}

	/**
	 * @return DateTime the $endTime
	 */
	public function getEndTime() {
		return $this->endTime;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime($endTime) {
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
	 * @return \Entity\TrainingPlan\Report the $trainingPlanReport
	 */
	public function getTrainingPlanReport() {
		return $this->trainingPlanReport;
	}

	/**
	 * @param \Entity\TrainingPlan\Report $trainingPlanReport
	 */
	public function setTrainingPlanReport(\Entity\TrainingPlan\Report $trainingPlanReport) {
		$this->trainingPlanReport = $trainingPlanReport;
	}

	/**
	 * @return \Entity\Exercise the $exercise
	 */
	public function getExercise() {
		return $this->exercise;
	}

	/**
	 * @param Exercise $exercise
	 */
	public function setExercise($exercise) {
		$this->exercise = $exercise;
	}
	
	/**
	 * @return ArrayCollection the $trackPoints
	 */
	public function getTrackPoints() {
		return $this->trackPoints;
	}

	/**
	 * @param Track $trackPoints
	 */
	public function setTrackPoints($trackPoints) {
		$this->trackPoints = $trackPoints;
	}
	
	public function addTrackPoint(\Entity\Exercise\TrackPoint $trackPoint) {
		$this->trackPoints[] = $trackPoint;
		$trackPoint->setReport($this);
	}

	public function getAverageSpeed() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		
		$speed = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			$speed += $trackPoint->getSpeed();
		}
		
		return round($speed / count($this->getTrackPoints()), 2);
	}
	
	public function getHighestSpeed() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		
		$speed = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			if ($speed < $trackPoint->getSpeed()) {
				$speed = $trackPoint->getSpeed();
			}
		}
		
		return $speed;
	}
	
	public function getAveragePace() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		
		$pace = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			$pace += $trackPoint->getPulse();
		}
		
		return round($pace / count($this->getTrackPoints()), 2);
	}
	
	public function getHighestPace() {
		$pace = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			if ($pace < $trackPoint->getPulse()) {
				$pace = $trackPoint->getPulse();
			}
		}
		
		return $pace;
	}
	
	public function getAverageHeartRate() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		
		$heartRate = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			$heartRate += $trackPoint->getHeart();
		}
		
		return round($heartRate / count($this->getTrackPoints()), 2);
	}
	
	public function getHighestHeartRate() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		
		$heartRate = 0;
		foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
			if ($heartRate < $trackPoint->getHeeart()) {
				$heartRate = $trackPoint->getHeeart();
			}
		}
		
		return $heartRate;
	}

	public function getTrackPoinstDistance() {
	    if (count($this->getTrackPoints()) == 0) {
	    	return 0;
	    }
	    
	    $distance = 0;
	    foreach ($this->getTrackPoints() as $trackPoint) /* @var $trackPoint \Entity\Exercise\TrackPoint */ {
	    	$distance += $trackPoint->getDistanceToLastPoint();
	    }
	    
	    return $distance;
	}
	
	public function getTrackPointsDuration() {
		if (count($this->getTrackPoints()) == 0) {
			return 0;
		}
		 
		$duration = $this->trackPoints[count($this->getTrackPoints()) - 1]->getTime()->diff($this->trackPoints[0]->getTime())->format('%s');
		 
		return $duration;
	}
}
