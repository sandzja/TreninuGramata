<?php

namespace Entity;

/**
 * @Entity
 */
class Goal extends AbstractEntity {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $distance;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $duration;
	
	/**
	 * @Column(name="is_challenge", type="integer")
	 * @var boolean
	 */
	protected $isChallenge;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;
	
	public function __construct() {
		
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @return string the $distance
	 */
	public function getDistance() {
		return $this->distance;
	}

	/**
	 * @param string $distance
	 */
	public function setDistance($distance) {
		$this->distance = $distance;
	}

	/**
	 * @return string the $duration
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * @param string $duration
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}
	
	/**
	 * @return boolean $isChallenge
	 */
	public function isChallenge() {
		return $this->isChallenge;
	}

	
	/**
	 * @param boolean $isChallenge
	 */
	public function setChallenge($isChallenge) {
		$this->isChallenge = $isChallenge;
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
	
}