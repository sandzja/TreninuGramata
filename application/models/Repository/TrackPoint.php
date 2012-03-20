<?php
namespace Repository;

use Repository\AbstractRepository;

class TrackPoint extends AbstractRepository {
	
	public function getByTimestamp($reportId, $timestamp) {
		$d = new \DateTime('@' . (int) $timestamp);
// 		$d->setTimezone(new \DateTimeZone('Etc/GMT-2'));
		
// 		print_r($d);
		
		$query = $this->createQueryBuilder('TrackPoint');
		$query->where('TrackPoint.report = :reportId')->setParameter('reportId', $reportId);
		$query->andWhere('TrackPoint.time > :timestamp ')->setParameter('timestamp', $d);

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