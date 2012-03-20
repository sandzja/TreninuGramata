<?php

namespace Entity\Challenge;

use Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ChallengeReport")
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
	 * @Column(name="didWinChallenge", type="integer")
	 * @var boolean
	 */
	protected $didWinChallenge;
	
	/**
	 * @OneToOne(targetEntity="\Entity\TrainingPlan\Report", mappedBy="challengeReport", cascade={"remove"})
	 * @JoinColumn(name="training_plan_report_id")
	 * @var \Entity\TrainingPlan\Report
	 */
	protected $trainingPlanReport;
	
	/**
	 * @OneToOne(targetEntity="\Entity\Challenge", cascade={"remove"})
	 * @JoinColumn(name="challenge_id")
	 * @var \Entity\Challenge
	 */
	protected $challenge;
	
	public function __construct() {
	}
	
	/**
	 * @return int $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return boolean $didWinChallenge
	 */
	public function didWinChallenge() {
		return $this->didWinChallenge;
	}

	/**
	 * @param boolean $didWinChallenge
	 */
	public function setWinChallenge($didWinChallenge) {
		$this->didWinChallenge = $didWinChallenge;
	}

	/**
	 * @return \Entity\TrainingPlan\Report $trainingPlanReport
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
	 * @return \Entity\Challenge $challenge
	 */
	public function getChallenge() {
		return $this->challenge;
	}

	/**
	 * @param \Entity\Challenge $challenge
	 */
	public function setChallenge(\Entity\Challenge $challenge) {
		$this->challenge = $challenge;
	}
	
}
