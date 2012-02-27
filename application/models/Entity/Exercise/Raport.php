<?php

namespace Entity\Exercise;

use Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Table(name="ExerciseRaport")
 * @Entity(repositoryClass="\Repository\Raport")
 */
class Raport extends AbstractEntity {
	
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
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $startTime;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $endTime;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Workout", inversedBy="raports")
	 * @JoinColumn(name="workoutId")
	 * @var \Entity\Workout
	 */
	protected $workout;
	
	/**
	 * @OneToOne(targetEntity="\Entity\Exercise", inversedBy="raport")
	 * @JoinColumn(name="exerciseId")
	 * @var Exercise;
	 */
	protected $exercise;
	
	/**
	 * @OneToMany(targetEntity="Entity\Exercise\TrackPoint", mappedBy="raport", cascade={"persist"})
	 * @var ArrayCollection
	 */
	protected $trackPoints;
	
	public function __construct() {
		$this->startTime = new \DateTime();
		$this->endTime = new \DateTime();
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
	 * @return \Entity\Workout the $workout
	 */
	public function getWorkout() {
		return $this->workout;
	}

	/**
	 * @param Workout $workout
	 */
	public function setWorkout(\Entity\Workout $workout) {
		$this->workout = $workout;
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
		$trackPoint->setRaport($this);
	}


}