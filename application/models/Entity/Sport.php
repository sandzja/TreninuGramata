<?php

namespace Entity;

/**
 * @Entity(repositoryClass="\Repository\Sport")
 */
class Sport extends AbstractEntity {
	
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
	protected $name;
	
	/**
	 * @Column(name="calories_factor", type="decimal")
	 * @var double
	 */
	protected $caloriesFactor;
	
	/**
	 * @Column(name="intensity_speed", type="decimal")
	 * @var double
	 */
	protected $intensitySpeed;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;
	
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
	 * @OneToMany(targetEntity="\Entity\TrainingPlan", mappedBy="sport", cascade={"persist"})
	 * @var ArrayCollection
	 */
	protected $trainingPlans;
	
	public function __construct() {
		
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
	 * @return string the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string the $caloriesFactor
	 */
	public function getCaloriesFactor() {
		return $this->caloriesFactor;
	}

	/**
	 * @param string $caloriesFactor
	 */
	public function setCaloriesFactor($caloriesFactor) {
		$this->caloriesFactor = $caloriesFactor;
	}
	
	/**
	 * @return number $intensitySpeed
	 */
	public function getIntensitySpeed() {
		return $this->intensitySpeed;
	}
	

	/**
	 * @param number $intensitySpeed
	 */
	public function setIntensitySpeed($intensitySpeed) {
		$this->intensitySpeed = $intensitySpeed;
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
	 * @return \Entity\User $user
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param \Entity\User $user
	 */
	public function setUser(\Entity\User $user) {
		$this->user = $user;
	}

	/**
	 * @return TrainingPlan the $trainingPlans
	 */
	public function getTrainingPlans() {
		return $this->trainingPlans;
	}
	
	
	public function addTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		$this->trainingPlans[] = $trainingPlan;
		$trainingPlan->setSport($this);
	}
}