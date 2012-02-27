<?php
namespace Service;

use Service\AbstractService;
class Exercise extends AbstractService {
	
	/**
	 * Fetch  entity
	 * @param int ID
	 * @return \Entity\Exercise
	 */
	public function getExercise($id) {
		return $this->em->find('\Entity\Exercise', (int) $id);
	}
	
	public function persistExercise(\Entity\Exercise $exercise) {
		$this->em->persist($exercise);
		$this->em->flush();
		
		return $exercise;
	}
	
	public function addExerciseToTrainingPlan(\Entity\Exercise $exercise, \Entity\TrainingPlan $trainigPlan) {
		$trainigPlan->addExercise($exercise);
		$this->em->persist($exercise);
	}
	
	/**
	 * Fetch  entity
	 * @param int ID
	 * @return \Entity\Exercise\Report
	 */
	public function getReport($id) {
		return $this->em->find('\Entity\Exercise\Report', (int) $id);
	}
	
	public function persistReport(\Entity\Exercise\Report $exerciseReport) {
		$this->em->persist($exerciseReport);
		
		return $exerciseReport;
	}
	
	/**
	 * Fetch  entity
	 * @param int ID
	 * @return \Entity\Exercise\TrackPoint
	 */
	public function getTrackPoint($id) {
		return $this->em->find('\Entity\Exercise\TrackPoint', (int) $id);
	}
	
	public function persistTrackPoint(\Entity\Exercise\TrackPoint $trackPoint) {
		$this->em->persist($trackPoint);
		
		return $trackPoint;
	}
}
?>