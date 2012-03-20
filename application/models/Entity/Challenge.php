<?php
namespace Entity;

use Entity\AbstractEntity;

/**
 * @Entity
 */
class Challenge extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @Column(type="datetime")
	 * @var \DateTime
	 */
	protected $date;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Record", inversedBy="challenges")
	 * @JoinColumn(name="record_id")
	 * @var \Entity\Record
	 */
	protected $record;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Workout", inversedBy="challenges", cascade={"remove"})
	 * @JoinColumn(name="workout_id")
	 * @var \Entity\Workout
	 */
	protected $workout;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\TrainingPlan", inversedBy="challenges")
	 * @JoinColumn(name="training_plan_id")
	 * @var \Entity\TrainingPlan
	 */
	protected $trainingPlan;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="challenges")
	 * @JoinColumn(name="user_id")
	 * @var \Entity\User
	 */
	protected $user;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User")
	 * @JoinColumn(name="opponent_user_id")
	 * @var \Entity\User
	 */
	protected $opponentUser;
	
	/**
	 * @var \Entity\Feed\Post\Challenge
	 * @OneToOne(targetEntity="Entity\Feed\Post\Challenge", mappedBy="challenge")
	 */
	protected $feedPost;
	
	/**
	 * @var \Entity\Challenge\Report
	 * @OneToOne(targetEntity="\Entity\Challenge\Report", mappedBy="challenge")
	 */
	protected $challengeReport;
	
	public function __construct() {
		
	}
	
	/**
	 * @return int $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param \DateTime $date
	 */
	public function setDate(\DateTime $date) {
		$this->date = $date;
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
	public function setRecord(\Entity\Record $record = null) {
		$this->record = $record;
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
	public function setWorkout(\Entity\Workout $workout = null) {
		$this->workout = $workout;
	}

	/**
	 * @return \Entity\TrainingPlan $trainingPlan
	 */
	public function getTrainingPlan() {
		return $this->trainingPlan;
	}

	/**
	 * @param \Entity\TrainingPlan $trainingPlan
	 */
	public function setTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		$this->trainingPlan = $trainingPlan;
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
	 * @return \Entity\User $opponentUser
	 */
	public function getOpponentUser() {
		return $this->opponentUser;
	}

	/**
	 * @param \Entity\User $opponentUser
	 */
	public function setOpponentUser(\Entity\User $opponentUser) {
		$this->opponentUser = $opponentUser;
	}
	
	/**
	 * @return \Entity\Feed\Post\Challenge $feedPost
	 */
	public function getFeedPost() {
		return $this->feedPost;
	}
	

	/**
	 * @param \Entity\Feed\Post\Challenge $feedPost
	 */
	public function setFeedPost(\Entity\Feed\Post\Challenge $feedPost) {
		$this->feedPost = $feedPost;
	}
	
	/**
	 * @return \Entity\Challenge\Report $challengeReport
	 */
	public function getChallengeReport() {
		return $this->challengeReport;
	}

	/**
	 * @param Report $challengeReport
	 */
	public function setChallengeReport($challengeReport) {
		$this->challengeReport = $challengeReport;
	}



	
}