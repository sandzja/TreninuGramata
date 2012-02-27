<?php

namespace Service\DTO;

class Workout {
	
	public $sportId;
	public $trainingPlanId;
	public $location;
	public $distance;
	public $unit;
	public $duration;
	public $comment;
	public $isPrivate;
	public $sendFacebook;
	public $sendTwitter;
	public $trackPoints = array ();
	public $name;
	public $startTime;
	public $endTime;
	public $alt;
	public $rating;
	public $exerciseId;
	public $userId;
	
	public $planReportId;
	public $calories;
	public $heartRate;
	public $pace;
	public $playlist;
	
	public $synced = false;
}