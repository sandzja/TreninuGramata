<?php
namespace Repository;

use Repository\AbstractRepository;

class TrainingPlan extends AbstractRepository {
	
	public function getBySport($sportId, \Entity\User $user) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->where('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		$query->andWhere('TrainingPlan.user = :userId')->setParameter('userId', $user);
		return $query->getQuery()->getResult();
	}
	
	public function getFriendsTrainingPlans(\Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->join('TrainingPlan.user', 'FriendUser');
		$query->join('FriendUser.followers', 'CurrentUser');
// 		$query->join('TrainingPlan.user', 'CurrentUser2');
		$query->join('TrainingPlan.feedPost', 'Post');
		$query->where('CurrentUser.id = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Post.isPrivate = :isPrivate')->setParameter('isPrivate', false);
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
	
		return $query->getQuery()->getResult();
	}
	
	public function getDatabaseTrainingPlans($limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('User.isFeatured = :isFeatured')->setParameter('isFeatured', true);
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}

		return $query->getQuery()->getResult();
	}
	
	public function searchUserTrainingPlans($name, $sportId = null, $intensity = null, \Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('TrainingPlan.user = :user')->setParameter('user', $user);
		$query->andWhere('TrainingPlan.name LIKE :name')->setParameter('name', '%' . $name . '%');
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
	
		if ($intensity != null) {
			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
			$query->groupBy('TrainingPlan.id');
		}
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
	
		return $query->getQuery()->getResult();
	}
	
	public function searchFriendsTrainingPlans($name, $sportId = null, $intensity = null, \Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->leftJoin('TrainingPlan.user', 'FriendUser');
		$query->leftJoin('FriendUser.followers', 'CurrentUser');
		$query->leftJoin('TrainingPlan.user', 'CurrentUser2');
		$query->leftJoin('TrainingPlan.feedPost', 'Post');
		$query->where('CurrentUser.id = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Post.isPrivate = :isPrivate')->setParameter('isPrivate', false);
		$query->andWhere('TrainingPlan.name LIKE :name')->setParameter('name', '%' . $name . '%');
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
		
		if ($intensity != null) {
			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
			$query->groupBy('TrainingPlan.id');
		}
		
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
	
		return $query->getQuery()->getResult();
	}
	
	public function searchDatabaseTrainingPlans($name, $sportId = null, $intensity = null, \Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('User.isFeatured = :isFeatured')->setParameter('isFeatured', true);
		$query->andWhere('TrainingPlan.name LIKE :name')->setParameter('name', '%' . $name . '%');
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
	
		if ($intensity != null) {
			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
			$query->groupBy('TrainingPlan.id');
		}
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
		
		return $query->getQuery()->getResult();
	}
}