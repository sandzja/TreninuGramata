<?php
namespace Repository;

use Doctrine\ORM\Query\Expr\GroupBy;

use Doctrine\ORM\Query\ResultSetMapping;

use Repository\AbstractRepository;

class Workout extends AbstractRepository {
	
	public function getOverallTimeGraph(\Entity\User $user) {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('time', 'time');
		$rsm->addScalarResult('week', 'week');
		$rsm->addScalarResult('seconds', 'seconds');
		
		$query = $this->_em->createNativeQuery('
			SELECT
				SEC_TO_TIME(SUM(Workout.duration)) as time,
				SUM(Workout.duration) as seconds,
				WEEK(start_time) as week
			FROM
				Workout
			WHERE
				user_id = ? AND
				end_time IS NOT NULL
			GROUP BY
				YEARWEEK(start_time)
			ORDER BY
				YEARWEEK(start_time) DESC
			LIMIT 4
			'
		, $rsm);
		$query->setParameter(1, $user->getId());
		
		return $query->getResult();
	}
	
	public function getDistanceGraph(\Entity\User $user) {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('distance', 'distance');
		$rsm->addScalarResult('week', 'week');
	
		$query = $this->_em->createNativeQuery('
			SELECT
				SUM(distance) as distance,
				WEEK(start_time) as week
			FROM
				Workout
			WHERE
				user_id = ? AND
				distance IS NOT NULL
			GROUP BY
				YEARWEEK(start_time)
			ORDER BY
				YEARWEEK(start_time) DESC
			LIMIT 4
			'
		, $rsm);
		$query->setParameter(1, $user->getId());
	
		return $query->getResult();
	}
	
	public function getWorkoutGraph(\Entity\User $user) {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('workout', 'workout');
		$rsm->addScalarResult('week', 'week');
	
		$query = $this->_em->createNativeQuery('
			SELECT
				count(id) as workout,
				WEEK(start_time) as week
			FROM
				Workout
			WHERE
				user_id = ?
			GROUP BY
				YEARWEEK(start_time)
			ORDER BY
				YEARWEEK(start_time) DESC
			LIMIT 4
				'
				, $rsm);
		$query->setParameter(1, $user->getId());
	
		return $query->getResult();
	}
	
	public function getGoaledWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.trainingPlanReports', 'TrainingPlanReport');
		$query->join('TrainingPlanReport.trainingPlan', 'TrainingPlan');
		$query->where('TrainingPlan.hasWorkoutGoal = 1');
		$query->andWhere('Workout.user = :userId')->setParameter('userId', $user->getId());
		$query->orderBy('Workout.id', 'DESC');
		
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
		
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
		
		return $query->getQuery()->getResult();
	}
	
	public function getChallengedWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.challenge', 'Challenge');
		$query->andWhere('Workout.user = :userId')->setParameter('userId', $user->getId());
		$query->orderBy('Workout.id', 'DESC');
		
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
		
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
		
		return $query->getQuery()->getResult();
	}
	
	public function getRecordedWorkouts(\Entity\User $user, $limit = null, $offset = null) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.record', 'Record');
		$query->andWhere('Record.user = :userId')->setParameter('userId', $user->getId());
		$query->orderBy('Workout.id', 'DESC');
		
		if ($limit != null) {
			$query->setMaxResults($limit);
		}
		
		if ($offset != null) {
			$query->setFirstResult($offset);
		}
		
		return $query->getQuery()->getResult();
	}
	
	public function getDailyWorkoutGraphs(\Entity\User $user, \DateTime $endTime, $sportId) {
		// TODO: implement endTime
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('time', 'time');
		$rsm->addScalarResult('date', 'date');
		$rsm->addScalarResult('seconds', 'seconds');
		
		$query = $this->_em->createNativeQuery('
			SELECT
				SEC_TO_TIME(SUM(Workout.duration)) as time,
				SUM(Workout.duration) as seconds,
				DATE(Workout.start_time) as date
			FROM
				Workout
			JOIN TrainingPlanReport ON TrainingPlanReport.workout_id = Workout.id
			JOIN TrainingPlan ON TrainingPlan.id = TrainingPlanReport.training_plan_id
			WHERE
				Workout.user_id = :userId AND
				Workout.end_time IS NOT NULL
				' . ($sportId != null ? ' AND TrainingPlan.sport_id = :sportId' : '') . '
			GROUP BY
				DATE(Workout.start_time)
			ORDER BY
				DATE(Workout.start_time) DESC
			LIMIT 30
			'
		, $rsm);
		$query->setParameter('userId', $user->getId());
		if ($sportId != null) {
			$query->setParameter('sportId', $sportId);
		}
		
		return $query->getResult();
	}
	
	public function getWeeklyWorkoutGraphs(\Entity\User $user, \DateTime $endTime, $sportId) {
		// TODO: implement endTime
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('week', 'week');
		$rsm->addScalarResult('time', 'time');
		$rsm->addScalarResult('seconds', 'seconds');
	
		$query = $this->_em->createNativeQuery('
			SELECT
				SEC_TO_TIME(SUM(Workout.duration)) as time,
				SUM(Workout.duration) as seconds,
				YEARWEEK(Workout.start_time) as week
			FROM
				Workout
			JOIN TrainingPlanReport ON TrainingPlanReport.workout_id = Workout.id
			JOIN TrainingPlan ON TrainingPlan.id = TrainingPlanReport.training_plan_id
			WHERE
				Workout.user_id = :userId AND
				Workout.end_time IS NOT NULL
				' . ($sportId != null ? ' AND TrainingPlan.sport_id = :sportId' : '') . '
			GROUP BY
				YEARWEEK(Workout.start_time)
			ORDER BY
				YEARWEEK(Workout.start_time) DESC
			LIMIT 30
				'
				, $rsm);
		$query->setParameter('userId', $user->getId());
		if ($sportId != null) {
			$query->setParameter('sportId', $sportId);
		}
	
		return $query->getResult();
	}
	
	public function getMonthlyWorkoutGraphs(\Entity\User $user, \DateTime $endTime, $sportId) {
		// TODO: implement endTime
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('month', 'month');
		$rsm->addScalarResult('year', 'year');
		$rsm->addScalarResult('time', 'time');
		$rsm->addScalarResult('seconds', 'seconds');
	
		$query = $this->_em->createNativeQuery('
			SELECT
				SEC_TO_TIME(SUM(Workout.duration)) as time,
				SUM(Workout.duration) as seconds,
				MONTH(Workout.start_time) as month,
				YEAR(Workout.start_time) as year
			FROM
				Workout
			JOIN TrainingPlanReport ON TrainingPlanReport.workout_id = Workout.id
			JOIN TrainingPlan ON TrainingPlan.id = TrainingPlanReport.training_plan_id
			WHERE
				Workout.user_id = :userId AND
				Workout.end_time IS NOT NULL
				' . ($sportId != null ? ' AND TrainingPlan.sport_id = :sportId' : '') . '
			GROUP BY
				MONTH(Workout.start_time)
			ORDER BY
				MONTH(Workout.start_time) DESC
			LIMIT 30
				'
				, $rsm);
		$query->setParameter('userId', $user->getId());
		if ($sportId != null) {
			$query->setParameter('sportId', $sportId);
		}
	
		return $query->getResult();
	}
	
	public function getWorkoutsByUserAndSport($userId, $sportId) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.trainingPlanReports', 'TrainingPlanReport');
		$query->where('TrainingPlanReport.sport = :sportId')->setParameter('sportId', $sportId);
		$query->andWhere('Workout.user = :userId')->setParameter('userId', $userId);
		
		return $query->getQuery()->getResult();
	}

	public function getCalendarWorkoutsByUser(\Entity\User $user, \DateTime $date) {
		$query = $this->createQueryBuilder('Workout');
		$query->where('Workout.user = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Workout.startTime >= :startTime')->setParameter('startTime', new \DateTime('first day of ' . $date->format('M') . ' ' . $date->format('Y')));
		$query->andWhere('Workout.startTime <= :endTime')->setParameter('endTime', new \DateTime('last day of ' . $date->format('M') . ' ' . $date->format('Y')));
		
		return $query->getQuery()->getResult();
	}
	
	public function getCalendarGoaledWorkouts(\Entity\User $user, \DateTime $date) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.trainingPlanReports', 'TrainingPlanReport');
		$query->join('TrainingPlanReport.trainingPlan', 'TrainingPlan');
		$query->where('TrainingPlan.hasWorkoutGoal = 1');
		$query->andWhere('Workout.user = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Workout.startTime >= :startTime')->setParameter('startTime', new \DateTime('first day of ' . $date->format('M') . ' ' . $date->format('Y')));
		$query->andWhere('Workout.startTime <= :endTime')->setParameter('endTime', new \DateTime('last day of ' . $date->format('M') . ' ' . $date->format('Y')));
		
		return $query->getQuery()->getResult();
	}
	
	public function getCalendarChallengedWorkouts(\Entity\User $user, \DateTime $date) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.challenge', 'Challenge');
		$query->andWhere('Workout.user = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Workout.startTime >= :startTime')->setParameter('startTime', new \DateTime('first day of ' . $date->format('M') . ' ' . $date->format('Y')));
		$query->andWhere('Workout.startTime <= :endTime')->setParameter('endTime', new \DateTime('last day of ' . $date->format('M') . ' ' . $date->format('Y')));

		return $query->getQuery()->getResult();
	}
	
	public function getCalendarRecordedWorkouts(\Entity\User $user, \DateTime $date) {
		$query = $this->createQueryBuilder('Workout');
		$query->join('Workout.record', 'Record');
		$query->andWhere('Record.user = :userId')->setParameter('userId', $user->getId());
		$query->andWhere('Workout.startTime >= :startTime')->setParameter('startTime', new \DateTime('first day of ' . $date->format('M') . ' ' . $date->format('Y')));
		$query->andWhere('Workout.startTime <= :endTime')->setParameter('endTime', new \DateTime('last day of ' . $date->format('M') . ' ' . $date->format('Y')));
		
		return $query->getQuery()->getResult();
	}
}