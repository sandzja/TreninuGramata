<?php
namespace Entity;

use Entity\AbstractEntity;

/**
 * @Entity
 */
class Exercise extends AbstractEntity {
	
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
	 * @Column(type="integer")
	 * @var integer
	 */
	protected $intensity;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $note;
	
	/**
	 * @Column(name="is_synced", type="boolean")
	 * @var boolean
	 */
	protected $isSynced = false;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\TrainingPlan", inversedBy="exercises")
	 * @JoinColumn(name="trainingPlanId")
	 * @var TrainingPlan
	 */
	protected $trainingPlan;
	
	/**
	 * @var \Entity\Goal
	 * @OneToOne(targetEntity="Entity\Goal", mappedBy="exercise", cascade={"persist"})
	 * @JoinColumn(name="goal_id")
	 */
	protected $goal;
	
	const INTENSITY_NONE = 0;
	const INTENSITY_LOW = 1;
	const INTENSITY_MEDIUM = 2;
	const INTENSITY_HIGH = 3;
	const INTENSITY_WC = 4;
	
	public static $intensities = array (
			self::INTENSITY_NONE => 'None',
			self::INTENSITY_LOW => 'Low',
			self::INTENSITY_MEDIUM => 'Medium',
			self::INTENSITY_HIGH => 'High',
			self::INTENSITY_WC => 'Warmup / Cooldown',
			);
	
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
	 * @return number $intensity
	 */
	public function getIntensity() {
		return $this->intensity;
	}
	

	/**
	 * @param number $intensity
	 */
	public function setIntensity($intensity) {
		$this->intensity = $intensity;
	}

	/**
	 * @return string $note
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * @param string $note
	 */
	public function setNote($note) {
		$this->note = $note;
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
	 * @return TrainingPlan the $trainingPlan
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
	 * @return Goal the $goal
	 */
	public function getGoal() {
		return $this->goal;
	}

	/**
	 * @param Goal $goal
	 */
	public function setGoal($goal) {
		$this->goal = $goal;
	}
	
}