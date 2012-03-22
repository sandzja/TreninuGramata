<?php

namespace Entity;

use Entity\AbstractEntity;

/**
 * Entity\Record
 *
 * @Table(name="Record")
 * @Entity(repositoryClass="\Repository\Record")
 */
class Record extends AbstractEntity {
	/**
	 * @var integer $id
	 *
	 * @Column(name="id", type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var float $distance
	 *
	 * @Column(name="distance", type="float", nullable=false)
	 */
	protected $distance;

	/**
	 * @var float $duration
	 *
	 * @Column(name="duration", type="integer", nullable=false)
	 */
	protected $duration;

	/**
	 * @var boolean $isTimeRecord
	 *
	 * @Column(name="is_time_record", type="boolean", nullable=false)
	 */
	protected $isTimeRecord;
	
	/**
	 * @var boolean $isMiles
	 *
	 * @Column(name="is_miles", type="boolean", nullable=false)
	 */
	protected $isMiles;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;

	/**
	 * @var \Entity\Sport
	 *
	 * @ManyToOne(targetEntity="Entity\Sport")
	 * @JoinColumns({
	 *   @JoinColumn(name="sport_id", referencedColumnName="id")
	 * })
	 */
	protected $sport;

	/**
	 * @var \Entity\User
	 *
	 * @ManyToOne(targetEntity="Entity\User")
	 * @JoinColumns({
	 *   @JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 */
	protected $user;
	
	/**
	 * @var \Entity\Workout
	 * @OneToOne(targetEntity="Entity\Workout", mappedBy="record", cascade={"persist"})
	 * @JoinColumn(name="workout_id")
	 */
	protected $workout;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set distance
	 *
	 * @param float $distance
	 */
	public function setDistance($distance) {
		$this->distance = $distance;
	}

	/**
	 * Get distance
	 *
	 * @return float
	 */
	public function getDistance() {
		return $this->distance;
	}

	/**
	 * Set duration
	 *
	 * @param float $duration
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * Get duration
	 *
	 * @return float
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * Set isTimeRecord
	 *
	 * @param boolean $isTimeRecord
	 */
	public function setIsTimeRecord($isTimeRecord) {
		$this->isTimeRecord = $isTimeRecord;
	}

	/**
	 * Get isTimeRecord
	 *
	 * @return boolean
	 */
	public function isTimeRecord() {
		return $this->isTimeRecord;
	}

	/**
	 * @return boolean $isMiles
	 */
	public function isMiles() {
		return $this->isMiles;
	}

	/**
	 * @param boolean $isMiles
	 */
	public function setIsMiles($isMiles) {
		$this->isMiles = $isMiles;
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
	 * Set sport
	 *
	 * @param \Entity\Sport $sport
	 */
	public function setSport(\Entity\Sport $sport) {
		$this->sport = $sport;
	}

	/**
	 * Get sport
	 *
	 * @return \Entity\Sport
	 */
	public function getSport() {
		return $this->sport;
	}

	/**
	 * Set user
	 *
	 * @param \Entity\User $user
	 */
	public function setUser(\Entity\User $user) {
		$this->user = $user;
	}

	/**
	 * Get user
	 *
	 * @return \Entity\User
	 */
	public function getUser() {
		return $this->user;
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

}