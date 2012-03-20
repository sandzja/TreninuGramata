<?php
namespace Entity;

use Doctrine\Common\Cache\ArrayCache;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="\Repository\TrainingPlan")
 */
class TrainingPlan extends AbstractEntity {
	
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
	 * @Column(type="datetime")
	 * @var \DateTime
	 */
	protected $date;
	
	/**
	 * @Column(name="execution_order", type="integer")
	 * @var int
	 */
	protected $executionOrder;
	
	/**
	 * @Column(name="deleted_time", type="datetime")
	 * @var \DateTime
	 */
	protected $deletedTime;
	
	/**
	 * @Column(name="is_default", type="integer")
	 * @var boolean
	 */
	protected $isDefault;
	
	/**
	 * @Column(name="has_workout_goal", type="integer")
	 * @var boolean
	 */
	protected $hasWorkoutGoal;
	
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
	
	/**
	 * @Column(name="is_featured", type="boolean")
	 * @var boolean
	 */
	protected $isFeatured = false;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="trainingPlans")
	 * @JoinColumn(name="user_id")
	 * @var User
	 */
	protected $user;
	
	/**
	 * @OneToMany(targetEntity="\Entity\TrainingPlan\Report", mappedBy="trainingPlan")
	 * @var ArrayCollection
	 */
	protected $trainingPlanReports;
	
	/**
	 * @OneToMany(targetEntity="\Entity\Exercise", mappedBy="trainingPlan", cascade={"persist"})
	 * @var ArrayCollection
	 */
	protected $exercises;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Sport", inversedBy="trainingPlans")
	 * @JoinColumn(name="sport_id")
	 * @var \Entity\Sport
	 */
	protected $sport;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\TrainingPlan", inversedBy="trainingPlanCopies")
	 * @JoinColumn(name="original_training_plan_id")
	 * @var \Entity\TrainingPlan
	 */
	protected $originalTrainingPlan;
	
	/**
	 * @OneToMany(targetEntity="\Entity\TrainingPlan", mappedBy="originalTrainingPlan", fetch="EXTRA_LAZY")
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $trainingPlanCopies;
	
	/**
	 * @var \Entity\Feed\Post\TrainingPlan
	 * @OneToOne(targetEntity="Entity\Feed\Post\TrainingPlan", mappedBy="trainingPlan")
	 */
	protected $feedPost;
	
	public function __construct() {
		$this->date = new \DateTime();
		$this->trainingPlanReports = new ArrayCollection();
		$this->exercises = new ArrayCollection();
		$this->trainingPlanCopies = new ArrayCollection();
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
	 * @return \DateTime the $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param DateTime $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * @return int the $executionOrder
	 */
	public function getExecutionOrder() {
		return $this->executionOrder;
	}

	/**
	 * @param int $executionOrder
	 */
	public function setExecutionOrder($executionOrder) {
		$this->executionOrder = $executionOrder;
	}

	/**
	 * @return DateTime the $deletedTime
	 */
	public function getDeletedTime() {
		return $this->deletedTime;
	}

	/**
	 * @param DateTime $deletedTime
	 */
	public function setDeletedTime($deletedTime) {
		$this->deletedTime = $deletedTime;
	}

	/**
	 * @return boolean the $isDefault
	 */
	public function isDefault() {
		return $this->isDefault;
	}
	
	/**
	 * @param boolean $isDefault
	 */
	public function setDefault($isDefault) {
		$this->isDefault = $isDefault;
	}
	
	/**
	 * @return boolean $hasWorkoutGoal
	 */
	public function hasWorkoutGoal() {
		return $this->hasWorkoutGoal;
	}
	

	/**
	 * @param boolean $hasWorkoutGoal
	 */
	public function setWorkoutGoal($hasWorkoutGoal) {
		$this->hasWorkoutGoal = $hasWorkoutGoal;
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
	
	/**
	 * @return boolean $isFeatured
	 */
	public function isFeatured() {
		return $this->isFeatured;
	}
	
	
	/**
	 * @param boolean $isFeatured
	 */
	public function setFeatured($isFeatured = true) {
		$this->isFeatured = $isFeatured;
	}
	
	/**
	 * @return User the $user
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * @return ArrayCollection the $trainingPlanReports
	 */
	public function getTrainingPlanReports() {
		return $this->trainingPlanReports;
	}

	/**
	 * @param \Enity\TrainingPlan\Report $trainingPlanReport
	 */
	public function addTrainingPlanReport(\Entity\TrainingPlan\Report $trainingPlanReport) {
		$this->trainingPlanReports[] = $trainingPlanReport;
		$trainingPlanReport->setTrainingPlan($this);
	}

	/**
	 * @return ArrayCollection the $exercises
	 */
	public function getExercises() {
		return $this->exercises;
	}

	/**
	 * @param ArrayCollection $exercises
	 */
	public function setExercises($exercises) {
		$this->exercises = $exercises;
	}

	public function addExercise(Exercise $exercise) {
		$this->exercises[] = $exercise;
		$exercise->setTrainingPlan($this);
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
	 * @return \Entity\TrainingPlan $originalTrainingPlan
	 */
	public function getOriginalTrainingPlan() {
		return $this->originalTrainingPlan;
	}

	/**
	 * @param \Entity\TrainingPlan $originalTrainingPlan
	 */
	public function setOriginalTrainingPlan($originalTrainingPlan) {
		$this->originalTrainingPlan = $originalTrainingPlan;
		
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $trainingPlanCopies
	 */
	public function getTrainingPlanCopies() {
		return $this->trainingPlanCopies;
	}

	/**
	 * @param \Entity\TrainingPlan $trainingPlan
	 */
	public function addTrainingPlanCopy(\Entity\TrainingPlan $trainingPlan) {
		$this->trainingPlanCopies[] = $trainingPlan;
		$trainingPlan->setOriginalTrainingPlan($this);
	}

	/**
	 * @return \Entity\Feed\Post\TrainingPlan $feedPost
	 */
	public function getFeedPost() {
		return $this->feedPost;
	}
	
	/**
	 * @param \Entity\Feed\Post\TrainingPlan $feedPost
	 */
	public function setFeedPost(\Entity\Feed\Post\TrainingPlan $feedPost) {
		$this->feedPost = $feedPost;
	}
	
	public function countDistances() {
		$distance = 0;
		foreach ($this->workouts as $workout) {
			$distance += $workout->getDistance();
		}
		
		return $distance;
	}
	
	public function countTime() {
		$time = 0;
		foreach ($this->workouts as $workout) {
			$stopTime = $workout->getEndTime();
			if ($stopTime == null) {
				$stopTime = new \DateTime();
			}
			if ($workout->getStartTime() != null) {
				$time += $workout->getStartTime()->diff($stopTime)->format('s');
			}
		}
		
		return $time;
	}
	
	public function countExerciseDuration() {
		$duration = 0;
		foreach ($this->exercises as $exercise) {
			if ($exercise->getGoal()->getDuration() != 0) {
				$duration += $exercise->getGoal()->getDuration();
			} else if ($exercise->getGoal()->getDistance() != 0 && $this->getSport()->getIntensitySpeed() != null) {
				$duration += $exercise->getGoal()->getDistance() / $this->getSport()->getIntensitySpeed();
			}
		}
	
		return $duration;
	}
	
}