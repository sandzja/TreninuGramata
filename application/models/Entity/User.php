<?php
namespace Entity;

use Doctrine\Common\Cache\ArrayCache;

use Doctrine\Common\Collections\ArrayCollection;

use Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @Entity(repositoryClass="\Repository\User")
 */
class User extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var integer
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
	protected $email;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $goal;
	
	/**
	 * @Column(name="goal_type", type="decimal")
	 * @var boolean
	 */
	protected $goalType;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $height;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $weight;
	
	/**
	 * @Column(type="date")
	 * @var DateTime
	 */
	protected $birthDate;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $facebookUserId;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $facebookAccessToken;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $twitterOAuthToken;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $twitterOAuthTokenSecret;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $twitterUserId;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $sessionId;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $sessionValidTime;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $updatedTime;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $profileImageUrl;
	
	/**
	 * @OneToMany(targetEntity="\Entity\TrainingPlan", mappedBy="user", cascade={"persist"}, fetch="EXTRA_LAZY")
	 * @var ArrayCollection
	 */
	protected $trainingPlans;
	
	/**
	 * @OneToMany(targetEntity="\Entity\Workout", mappedBy="user", cascade={"persist"}, fetch="EXTRA_LAZY")
	 * @OrderBy({"id" = "DESC"})
	 * @var ArrayCollection
	 */
	protected $workouts;
	
	/**
	 * @OneToMany(targetEntity="\Entity\Record", mappedBy="user", cascade={"persist"})
	 * @var ArrayCollection
	 * @OrderBy({"distance" = "ASC"})
	 */
	protected $records;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
	 * @OneToMany(targetEntity="\Entity\Feed\Post", mappedBy="author", fetch="EXTRA_LAZY")
	 * @OrderBy({"id" = "DESC"})
	 */
	protected $posts;
	
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ManyToMany(targetEntity="\Entity\User", fetch="EXTRA_LAZY", cascade={"persist"})
	 * @JoinTable(name="Friend",
     *	joinColumns={@JoinColumn(name="friend_user_id", referencedColumnName="id")},
     *	inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     * @OrderBy({"name" = "ASC"})
	 */
	protected $followers;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ManyToMany(targetEntity="\Entity\User", fetch="EXTRA_LAZY", cascade={"persist"})
	 * @JoinTable(name="Friend",
     *	joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *	inverseJoinColumns={@JoinColumn(name="friend_user_id", referencedColumnName="id")}
     * )
     * @OrderBy({"name" = "ASC"})
	 */
	protected $followings;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 *
	 * @OneToMany(targetEntity="\Entity\Sport", mappedBy="user", fetch="EXTRA_LAZY")
	 * @OrderBy({"id" = "DESC"})
	 */
	protected $sports;
	
	public function __construct() {
		$this->sessionId = session_id();
		$this->sessionValidTime = new \DateTime();
		$this->updatedTime = new \DateTime();
		$this->trainingPlans = new ArrayCollection();
		$this->workouts = new ArrayCollection();
		$this->records = new ArrayCollection();
		$this->posts = new ArrayCollection();
		$this->likes = new ArrayCollection();
		$this->followers = new ArrayCollection();
		$this->followings = new ArrayCollection();
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param integer $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return the $name
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
	 * @return the $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}
	
	/**
	 * @return the $goal
	 */
	public function getGoal() {
		return $this->goal;
	}

	/**
	 * @param string $goal
	 */
	public function setGoal($goal) {
		$this->goal = $goal;
	}

	/**
	 * @return boolean $goalType
	 */
	public function getGoalType() {
		return $this->goalType;
	}

	/**
	 * @param boolean $goalType
	 */
	public function setGoalType($goalType) {
		$this->goalType = $goalType;
	}

	/**
	 * @return the $height
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @param double $height
	 */
	public function setHeight($height) {
		$this->height = $height;
	}

	/**
	 * @return the $weight
	 */
	public function getWeight() {
		return $this->weight;
	}

	/**
	 * @param double $weight
	 */
	public function setWeight($weight) {
		$this->weight = $weight;
	}

	/**
	 * @return \DateTime $birthDate
	 */
	public function getBirtDate() {
		return $this->birthDate;
	}

	/**
	 * @param DateTime $birthDate
	 */
	public function setBirthDate($birthDate) {
		$this->birthDate = $birthDate;
	}

	/**
	 * @return the $facebookUserId
	 */
	public function getFacebookUserId() {
		return $this->facebookUserId;
	}

	/**
	 * @param string $facebookUserId
	 */
	public function setFacebookUserId($facebookUserId) {
		$this->facebookUserId = $facebookUserId;
	}

	/**
	 * @return string $facebookAccessToken
	 */
	public function getFacebookAccessToken() {
		return $this->facebookAccessToken;
	}

	/**
	 * @param string $facebookAccessToken
	 */
	public function setFacebookAccessToken($facebookAccessToken) {
		$this->facebookAccessToken = $facebookAccessToken;
	}

	/**
	 * @return the $twitterOAuthToken
	 */
	public function getTwitterOAuthToken() {
		return $this->twitterOAuthToken;
	}

	/**
	 * @param string $twitterOAuthToken
	 */
	public function setTwitterOAuthToken($twitterOAuthToken) {
		$this->twitterOAuthToken = $twitterOAuthToken;
	}

	/**
	 * @return the $twitterOAuthTokenSecret
	 */
	public function getTwitterOAuthTokenSecret() {
		return $this->twitterOAuthTokenSecret;
	}

	/**
	 * @param string $twitterOAuthTokenSecret
	 */
	public function setTwitterOAuthTokenSecret($twitterOAuthTokenSecret) {
		$this->twitterOAuthTokenSecret = $twitterOAuthTokenSecret;
	}

	/**
	 * @return the $twitterUserId
	 */
	public function getTwitterUserId() {
		return $this->twitterUserId;
	}

	/**
	 * @param string $twitterUserId
	 */
	public function setTwitterUserId($twitterUserId) {
		$this->twitterUserId = $twitterUserId;
	}

	/**
	 * @return the $sessionId
	 */
	public function getSessionId() {
		return $this->sessionId;
	}

	/**
	 * @return the $sessionValidTime
	 */
	public function getSessionValidTime() {
		return $this->sessionValidTime;
	}

	/**
	 * @param DateTime $sessionValidTime
	 */
	public function setSessionValidTime($sessionValidTime) {
		$this->sessionValidTime = $sessionValidTime;
	}
	
	/**
	 * @return \DateTime the $modifiedTime
	 */
	public function getUpdatedTime() {
		return $this->updatedTime;
	}

	/**
	 * @param \DateTime $modifiedTime
	 */
	public function setUpdatedTime($updatedTime) {
		$this->updatedTime = $updatedTime;
	}
	
	/**
	 * @return string $profileImageUrl
	 */
	public function getProfileImageUrl() {
		return $this->profileImageUrl;
	}

	/**
	 * @param string $profileImageUrl
	 */
	public function setProfileImageUrl($profileImageUrl) {
		$this->profileImageUrl = $profileImageUrl;
	}

	/**
	 * @return TrainingPlan the $trainingPlans
	 */
	public function getTrainingPlans() {
		return $this->trainingPlans;
	}

	/**
	 * @param TrainingPlan $trainingPlans
	 */
	public function setTrainingPlans($trainingPlans) {
		$this->trainingPlans = $trainingPlans;
	}

	public function addTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		$this->trainingPlans[] = $trainingPlan;
		$trainingPlan->setUser($this);
	}
	
	/**
	 * @return ArrayCollection the $workouts
	 */
	public function getWorkouts() {
		return $this->workouts;
	}

	/**
	 * @param ArrayCollection $workouts
	 */
	public function setWorkouts($workouts) {
		$this->workouts = $workouts;
	}

	public function addWorkout(\Entity\Workout $workout) {
		$this->workouts[] = $workout;
		$workout->setUser($this);
	}
	
	/**
	 * @return the $records
	 */
	public function getRecords() {
		return $this->records;
	}

	/**
	 * @param \Entity\Record
	 */
	public function addRecord(\Entity\Record $record) {
		$this->records[] = $record;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $posts
	 */
	public function getPosts() {
		return $this->posts;
	}

	/**
	 * @param \Entity\Feed\Post $post
	 */
	public function addPost($post) {
		$this->posts[] = $post;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $followers
	 */
	public function getFollowers() {
		return $this->followers;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection $following
	 */
	public function getFollowings() {
		return $this->followings;
	}

	/**
	 * @param \Entity\User $following
	 */
	public function addFollowing($following) {
		$this->followings[] = $following;
	}

	public function countDistances() {
		$distance = 0;
		foreach ($this->workouts as $workout) {
			$distance += $workout->getDistance();
		}
		
		return $distance;
	}
	
	public function countCalories() {
		$calories = 0;
		foreach ($this->workouts as $workout) /* @var $workout \Entity\Workout */ {
			foreach ($workout->getTrainingPlanReports() as $trainingPlanReport) /* @var $trainingPlanReport \Entity\TrainingPlan\Report */ {
				$calories += $trainingPlanReport->getBurnedCalories();
			}
		}
	
		return $calories;
	}
	
	public function countTime() {
		$time = 0;
		foreach ($this->workouts as $workout) {
			$time += $workout->getDuration();
		}
		
		return $time;
	}
	
	/**
	 * @return ArrayCollection $sports
	 */
	public function getSports() {
		return $this->sports;
	}

	
	/**
	 * @param ArrayCollection $sports
	 */
	public function addSport(\Entity\Sport $sport) {
		$this->sports[] = $sport;
		$sport->setUser($this);
	}


}