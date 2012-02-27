<?php
namespace Repository;

use Repository\AbstractRepository;

class TrackPoint extends AbstractRepository {
	
	public function getByTimestamp($reportId, $timestamp) {
		$query = $this->createQueryBuilder('TrackPoint');
		$query->where('TrackPoint.report = :reportId')->setParameter('reportId', $reportId);
		$query->andWhere('TrackPoint.time > :timestamp ')->setParameter('timestamp', new \DateTime('@' . (int) $timestamp));
		
		return $query->getQuery()->getResult();
	}
	
	
	public function getActives() {
		$query = $this->createQueryBuilder('TrackPoint');
		$query->join('TrackPoint.report', 'Report');
		$query->where('Report.startTime IS NOT NULL');
		$query->andWhere('Report.endTime IS NULL');
		$query->groupBy('Report.id');
		
		return $query->getQuery()->getResult();
	}
}