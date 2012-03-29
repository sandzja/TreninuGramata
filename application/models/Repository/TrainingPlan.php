<?php
namespace Repository;

use Doctrine\ORM\Query\Expr\GroupBy;

use Doctrine\ORM\Query\ResultSetMapping;

use Repository\AbstractRepository;

class TrainingPlan extends AbstractRepository {
	
	public function getBySport($sportId, \Entity\User $user) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->where('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		$query->andWhere('TrainingPlan.user = :userId')->setParameter('userId', $user);
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		return $query->getQuery()->getResult();
	}

	public function getUserTrainingPlans(\Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('TrainingPlan');
		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('TrainingPlan.user = :user')->setParameter('user', $user);
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
	
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
	
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		$query->groupBy('TrainingPlan.id');
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
	
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
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		//@TODO: Temp solution for not displaying basic tracking
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
		$query->groupBy('TrainingPlan.id');
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
// 		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('TrainingPlan.isFeatured = :isFeatured')->setParameter('isFeatured', true);
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		
		//@TODO: Temp solution for not displaying basic tracking
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
		$query->groupBy('TrainingPlan.id');
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
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
		
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
	
		if ($intensity != null) {
// 			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
			
		}
	
		$query->groupBy('TrainingPlan.id');
		
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
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		$query->orderBy('TrainingPlan.id', 'DESC');
	
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
		$query->groupBy('TrainingPlan.id');
		
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
		
		if ($intensity != null) {
// 			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
// 			$query->groupBy('TrainingPlan.id');
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
// 		$query->leftJoin('TrainingPlan.user', 'User');
		$query->where('TrainingPlan.isFeatured = :isFeatured')->setParameter('isFeatured', true);
		$query->andWhere('TrainingPlan.name LIKE :name')->setParameter('name', '%' . $name . '%');
		$query->andWhere('TrainingPlan.deletedTime IS NULL');
		$query->andWhere('TrainingPlan.hasWorkoutGoal = :hasWorkoutGoal')->setParameter('hasWorkoutGoal', false);
		$query->andWhere('TrainingPlan.isChallenge = :isChallenge')->setParameter('isChallenge', false);
		$query->orderBy('TrainingPlan.id', 'DESC');
		
		//@TODO: Temp solution for not displaying basic tracking
		$query->leftJoin('TrainingPlan.exercises', 'Exercise');
		$query->leftJoin('Exercise.goal', 'Goal');
		$query->andWhere('Goal.distance IS NOT NULL OR Goal.duration IS NOT NULL');
		$query->groupBy('TrainingPlan.id');
	
		if ($sportId != null) {
			$query->andWhere('TrainingPlan.sport = :sportId')->setParameter('sportId', $sportId);
		}
	
		if ($intensity != null) {
// 			$query->join('TrainingPlan.exercises', 'Exercise');
			$query->andWhere('Exercise.intensity = :intensity')->setParameter('intensity', $intensity);
// 			$query->groupBy('TrainingPlan.id');
		}
	
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
		
		return $query->getQuery()->getResult();
	}

	public function getMaxExecutionOrder(\Entity\User $user, \Entity\TrainingPlan $trainingPlan) {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('maxexecutionorder', 'maxexecutionorder');

		$query = $this->_em->createNativeQuery('
			SELECT
				max(execution_order) as maxexecutionorder
			FROM
				TrainingPlan
			WHERE
				user_id = ?
				and TrainingPlan.set_id = ?
				and TrainingPlan.deleted_time is NULL
			'
				, $rsm);

		$query->setParameter(1, $user->getId());
		$query->setParameter(2, $trainingPlan->getsetIds());

		$data = $query->getResult();

error_log(print_r($data,true));

		return $data[0]['maxexecutionorder'];
	}	
}