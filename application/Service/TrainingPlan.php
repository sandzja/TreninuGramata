<?php

namespace Service;

use Doctrine\Common\Collections\ArrayCollection;

class TrainingPlan extends AbstractService {
	
	/**
	 * Fetch training plan entity
	 * @param int ID
	 * @return \Entity\TrainingPlan
	 */
	public function getTrainingPlan($id) {
		return $this->em->find('\Entity\TrainingPlan', (int) $id);
	}
	
	public function persistTrainingPlan(\Entity\TrainingPlan $trainingPlan) {
		$this->em->persist($trainingPlan);
		
		return $trainingPlan;
	}
	
	public function deleteTrainingPlan(\Entity\TrainingPlan $trainingPlan, \DateTime $deletedTime) {
		$trainingPlan->setDeletedTime($deletedTime);
		$this->em->persist($trainingPlan);
	}
	
	public function createTrainingPlan(\Entity\TrainingPlan $trainingPlan, ArrayCollection $exercises) {
		foreach ($exercises as $exercise) {
			$trainingPlan->addExercise($exercise);
			$this->em->persist($exercise);
		}
		
		$this->em->persist($trainingPlan);
	}
}