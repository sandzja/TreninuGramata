<?php
namespace Service\Api;


use Entity\TrackPoint;

class TrackPoint {
	
	public function upload($TrackPoint) {
		$userApiService = new User();
		$userApiService->checkAndUpdateSession(@$TrackPoint['UserID'], @$TrackPoint['SessionID']);
		
		$exerciseService = new \Service\Exercise();
		
		$trackPoint = new TrackPoint();
		$time = new \DateTime();
		$time->setTimestamp(@$TrackPoint['TrackPointTime']);
		$trackPoint->setTime($time);
		$trackPoint->setAlt(@$TrackPoint['TrackPointAltitude']);
		$trackPoint->setLat(@$TrackPoint['TrackPointLatitude']);
		$trackPoint->setLon(@$TrackPoint['TrackPointLongitude']);
		$trackPoint->setDistanceToLastPoint(@$TrackPoint['TrackPointDistanceToLastPoint']);
		$trackPoint->setSpeed(@$TrackPoint['TrackPointSpeed']);
		$trackPoint->setHeart(@$TrackPoint['TrackPointHeart']);
		$trackPoint->setPulse(@$TrackPoint['TrackPointPulse']);
		$trackPoint->setRaport($exerciseService->getTrackPoint(@$TrackPoint['ExerciseReportID']));
		$exerciseService->persistTrackPoint($trackPoint);
		
		$response = \Zend_Json::encode(array (
			'Response' => 'OK',
		));
		
		return $response;
	}
	
}