<?php
namespace Repository;

use Repository\AbstractRepository;

class Sport extends AbstractRepository {
	
	public function getUserSports($userId) {
		$query = $this->createQueryBuilder('Sport');
		$query->where('Sport.user = :userId')->setParameter('userId', $userId);
		$query->orWhere('Sport.user IS NULL');
		
		return $query->getQuery()->getResult();
	}
	
	public function getDefaultAndUserSportsNotSynced($userId) {
		$query = $this->createQueryBuilder('Sport');
		$query->where('Sport.user = :userId AND Sport.isSynced = 0')->setParameter('userId', $userId);
		$query->orWhere('Sport.user IS NULL');
	
		return $query->getQuery()->getResult();
	}
}