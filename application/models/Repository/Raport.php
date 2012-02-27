<?php
namespace Repository;

use Repository\AbstractRepository;

class Raport extends AbstractRepository {
	
	public function getCurrentlyActiveRaport($workoutId) {
		$query = $this->createQueryBuilder('Raport');
		$query->where('Raport.workout = :workoutId')->setParameter('workoutId', $workoutId);
		$query->andwhere('Raport.startTime IS NOT NULL');
		$query->andwhere('Raport.endTime IS NULL');
		$query->setMaxResults(1);
		
		return $query->getQuery()->getSingleResult();
	}
	
}