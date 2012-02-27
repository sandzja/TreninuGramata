<?php
namespace Repository;

use Repository\AbstractRepository;

class Report extends AbstractRepository {
	
	public function getCurrentlyActiveReport($workoutId) {
		$query = $this->createQueryBuilder('Report');
		$query->join('Report.trainingPlanReport', 'TrainingPlanReport');
		$query->join('TrainingPlanReport.workout', 'Workout');
		$query->where('TrainingPlanReport.workout = :workoutId')->setParameter('workoutId', $workoutId);
		$query->andwhere('Workout.startTime IS NOT NULL');
		$query->andwhere('Workout.endTime IS NULL');
		$query->setMaxResults(1);
		
		return $query->getQuery()->getOneOrNullResult();
	}
	
}