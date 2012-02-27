<?php

namespace Entity\Feed\Post;


/**
 * Entity\Feed\TrainingPlan
 *
 * @Table(name="FeedTrainingPlan")
 * @Entity
 */
class TrainingPlan extends \Entity\Feed\Post {

	/**
	 * @var \Entity\TrainingPlan
	 * @OneToOne(targetEntity="Entity\TrainingPlan")
	 * @JoinColumn(name="training_plan_id")
	 */
	protected $trainingPlan;
	
	/**
	 * @return \Entity\TrainingPlan $trainingPlan
	 */
	public function getTrainingPlan() {
		return $this->trainingPlan;
	}

	/**
	 * @param \Entity\TrainingPlan
	 */
	public function setTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		$this->trainingPlan = $trainingPlan;
	}

	
}