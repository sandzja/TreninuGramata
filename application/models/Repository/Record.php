<?php
namespace Repository;

use Doctrine\ORM\Query\Expr\GroupBy;

use Doctrine\ORM\Query\ResultSetMapping;

use Repository\AbstractRepository;

class Record extends AbstractRepository {
	
	public function getUserRecordsByCriteria(array $record, \Entity\User $user, $sport = null) {
		$query = $this->createQueryBuilder('Record');
		
		$query->where('Record.isTimeRecord = :isTimeRecord')->setParameter('isTimeRecord', $record['isTimeRecord']);
		$query->andWhere('Record.isMiles = :isMiles')->setParameter('isMiles', $record['isInMiles']);
		$query->andWhere('Record.user = :user')->setParameter('user', $user);
		
		if ($record['duration'] == null) {
			$query->andWhere('Record.distance = :distance')->setParameter('distance', $record['distance']);
			$query->orderBy('Record.duration', 'asc');
		}
		
		if ($record['distance'] == null) {
			$query->andWhere('Record.duration = :duration')->setParameter('duration', $record['duration']);
			$query->orderBy('Record.distance', 'asc');
		}
		
		if ($sport != null) {
			$query->andWhere('Record.sport = :sport')->setParameter('sport', $sport);
			$query->setMaxResults(1);
			
			return $query->getQuery()->getOneOrNUllResult();
		}
		
		$query->groupBy('Record.sport');
		
		 
		return $query->getQuery()->getResult();
	}
	
	public function getRecord(\Entity\Sport $sport, \Entity\User $user = null) {
		$query = $this->createQueryBuilder('Record');
		$query->where('Record.sport = :sportId')->setParameter('sportId', $sport->getId());
		
		if ($user != null) {
			$query->andWhere('Record.user = :userId')->setParameter('userId', $user->getId());
		}
		
		$query->orderBy('Record.distance', 'asc');
		$query->orderBy('Record.duration', 'asc');
		
		$query->setMaxResults(1);
		
		return $query->getQuery()->getOneOrNullResult();
	}
	
	public function getRecordByFriendName(\Entity\Sport $sport, $friendName) {
		$query = $this->createQueryBuilder('Record');
		$query->join('Record.user', 'User');
		
		$query->where('User.name LIKE :name')->setParameter('name', '%' . $friendName . '%');
		$query->andWhere('Record.sport = :sportId')->setParameter('sportId', $sport->getId());
		$query->join('User.followers', 'Follower');
		$query->andWhere('Follower.id = :currentUserId')->setParameter('currentUserId', $this->getCurrentUser()->getId());
		
		$query->orderBy('Record.distance', 'asc');
		$query->orderBy('Record.duration', 'asc');
		
		$query->setMaxResults(1);
		
		return $query->getQuery()->getOneOrNullResult();
	}

	public function getRecordPositions(\Entity\Record $record) {
	    $query = $this->createQueryBuilder('Record');
	    $query->where('Record.sport = :sport')->setParameter('sport', $record->getSport());
	    $query->andWhere('Record.distance = :distance')->setParameter('distance', $record->getDistance());
		$query->andWhere('Record.duration <= :duration')->setParameter('duration', $record->getDuration());
	    $query->orderBy('Record.duration', 'asc');
	    
	    return $query->getQuery()->getResult();
	}
}