<?php
namespace Entity;

use Entity\AbstractEntity;

/**
 * @Entity(repositoryClass="\Repository\Workout")
 */
class Workout extends AbstractEntity {
	
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
	 * @Column(type="string")
	 * @var string
	 */
	protected $location;
	
	/**
	 * @Column(type="string")
	 * @var string
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
	 * @Column(name="is_shared", type="integer")
	 * @var boolean
	 */
	protected $isShared;
	
	/**
	 * @Column(name="rating", type="integer")
	 * @var integer
	 */
	protected $rating;
	
	/**
	 * @Column(name="play_list", type="string")
	 * @var string
	 */
	protected $playList;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;
	
	/**
	 * @OneToMany(targetEntity="\Entity\TrainingPlan\Report", mappedBy="workout")
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $trainingPlanReports;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ManyToMany(targetEntity="\Entity\TrainingPlan", fetch="EXTRA_LAZY", cascade={"persist"})
	 * @JoinTable(name="TrainingPlanReport",
     *	joinColumns={@JoinColumn(name="workout_id", referencedColumnName="id")},
     *	inverseJoinColumns={@JoinColumn(name="training_plan_id", referencedColumnName="id")}
     * )
	 */
	protected $trainingPlans;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="workouts")
	 * @JoinColumn(name="user_id")
	 * @var \Entity\User
	 */
	protected $user;
	
	/**
	 * @var \Entity\Feed\Post\Workout
	 * @OneToOne(targetEntity="Entity\Feed\Post\Workout", mappedBy="workout")
	 */
	protected $feedPost;
	
	/**
	 * @var \Entity\Record
	 * @OneToOne(targetEntity="Entity\Record", mappedBy="workout")
	 */
	protected $record;
	
	/**
	 * @var \Entity\Challenge
	 * @OneToOne(targetEntity="Entity\Challenge", mappedBy="workout")
	 */
	protected $challenge;
	
	public function __construct() {
		
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
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
	 * @return string $location
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * @param string $location
	 */
	public function setLocation($location) {
		$this->location = $location;
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
	 * @return boolean the $isShared
	 */
	public function isShared() {
		return $this->isShared;
	}

	/**
	 * @param boolean $isShared
	 */
	public function setShared($isShared) {
		$this->isShared = $isShared;
	}

	/**
	 * @return number $rating
	 */
	public function getRating() {
		return $this->rating;
	}

	/**
	 * @param number $rating
	 */
	public function setRating($rating) {
		$this->rating = $rating;
	}

	/**
	 * @return string $playList
	 */
	public function getPlayList() {
		return $this->playList;
	}

	/**
	 * @param string $playList
	 */
	public function setPlayList($playList) {
		$this->playList = $playList;
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
	 * @return \Doctrine\Common\Collections\ArrayCollection the $trainingPlanReports
	 */
	public function getTrainingPlanReports() {
		return $this->trainingPlanReports;
	}
	
	/**
	 * @return \Entity\TrainingPlan\Report
	 */
	public function getTrainingPlanReport($i) {
		return $this->trainingPlanReports[$i];
	}

	/**
	 * @param TrainingPlan $trainingPlan
	 */
	public function addTrainingPlanReport(\Entity\TrainingPlan\Report $trainingPlanReport) {
		$this->trainingPlanReports[] = $trainingPlanReport;
		$trainingPlanReport->setWorkout($this);
	}

	/**
	 * @return \Entity\User the $user
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
	 * @return \Entity\Feed\Post\Workout $feedPost
	 */
	public function getFeedPost() {
		return $this->feedPost;
	}

	/**
	 * @param \Entity\Feed\Post\Workout $feedPost
	 */
	public function setFeedPost(\Entity\Feed\Post\Workout $feedPost) {
		$this->feedPost = $feedPost;
	}

	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $trainingPlans
	 */
	public function getTrainingPlans() {
		return $this->trainingPlans;
	}
	
	/**
	 * @param unknown_type $i
	 * @return \Entity\TrainingPlan $trainingPlan
	 */
	public function getTrainingPlan($i) {
		return $this->trainingPlans[$i];
	}
	
	/**
	 * @return \Entity\Record $record
	 */
	public function getRecord() {
		return $this->record;
	}
	

	/**
	 * @param \Entity\Record $record
	 */
	public function setRecord(\Entity\Record $record) {
		$this->record = $record;
	}
	
	/**
	 * @return \Entity\Challenge $challenge
	 */
	public function getChallenge() {
		return $this->challenge;
	}
	

	/**
	 * @param \Entity\Challenge $challenge
	 */
	public function setChallenge($challenge) {
		$this->challenge = $challenge;
	}


}