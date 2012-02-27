<?php

namespace Service\DTO;

class TrainingPlan {
	
	public $sportId;
	public $name;
	public $date;
	public $executionOrder;
	public $deletedTime;
	public $isDefault;
	public $hasWorkoutGoal;
	public $isChallenge;
	public $isPrivate;
	public $note;
	public $exercises = array ();
	public $userId;
	public $synced = false;
}