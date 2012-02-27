<?php

use Service\ServiceManager;
class WorkoutController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\Workout
	 */
	private $workoutService;
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function init() {
		$this->workoutService = ServiceManager::factory(new \Service\Workout());
		$this->userService = ServiceManager::factory(new \Service\User());
		
		$this->_helper->setActiveMenu('workout-plans');
	}
	
	public function indexAction() {
		$this->view->type = $this->_getParam('type', 'my');
		$this->view->name = $this->_getParam('name');
		$this->view->search = $this->_getParam('search');
	}
	
 	public function postsAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	if ($this->_getParam('search') != null) {
    		$this->view->trainingPlans = $this->workoutService->searchTrainingPlans($this->_getParam('type'), $this->_getParam('name'), $this->_getParam('sportId'), $this->_getParam('intensity'), $this->_getParam('userId'), $this->_getParam('type'), 10, $this->_getParam('page', 0) * 10);
    	} else {
    		$this->view->trainingPlans = $this->workoutService->getTrainingPlans($this->_getParam('userId'), $this->_getParam('type'), 10, $this->_getParam('page', 0) * 10);
    	}
    	
    	$this->view->workoutService = $this->workoutService;
    	$this->view->currentUser = $this->userService->getCurrentUser();
    }
	
	public function trackAction() {
		$this->_helper->setLayout('live_track');
		$this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
		
		$this->view->report = $this->workoutService->getCurrentlyActiveReport($this->_getParam('id'));
		$this->view->currentUser = $this->userService->getCurrentUser();
		$this->view->id = $this->_getParam('id');
		$this->view->workoutService = $this->workoutService;
		
		if ($this->view->report == null) {
			$this->_helper->redirector('index');
		}
	}
	
	public function getTrackDataAction() {
	    $this->_helper->disableView();
	    
	    if ($this->_request->isXmlHttpRequest()) {
		    $report = $this->workoutService->getCurrentlyActiveReport($this->_getParam('id'));
		    if ($report != null) {
			    $data = array (
			    	'duration' => $this->view->secondsToHours($report->getTrackPointsDuration()),
			    	'distance' => round($report->getTrackPoinstDistance() / 1000, 2),
			    	'speed' => $report->getAverageSpeed(),
			    	'heart' => $report->getAverageHeartRate(),
			    	'energy' => $report->getAveragePace(),
			    );
		    } else {
		    	$data = array ();
		    }
		    
		    echo Zend_Json::encode($data);
	    }
	}
	
	public function sendPepTalkAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
		$messageService = \Service\ServiceManager::factory(new \Service\Message());
		
		$workout = $this->workoutService->getWorkout($this->_getParam('workoutId'));
		$messageService->send($workout->getUser()->getId(), $this->_getParam('message'));
	}
	
 	public function getTrainingPlansAction() {
    	if ($this->_request->isXMLHttpRequest()) {
	 		$this->_helper->disableView();
	    	
	    	$trainingPlans = $this->workoutService->getTrainingPlansBySport($this->_getParam('sportId'));
	    	
	    	$plans = array ();
	    	foreach ($trainingPlans as $trainingPlan) {
	    		$plans[$trainingPlan->getId()] = $trainingPlan->getName();
	    	}
	    	
	    	echo Zend_Json::encode($plans);
    	}
    }
    
    /**
     * @deprecated throws exception
     */
    public function getGoalTypeAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableView();
    		 $exercise = $this->workoutService->getExerciseBySportAndTrainingPlan($this->_getParam('sportId'), $this->_getParam('trainingPlanId'));

    		 $types = array ();
    		 if ($exercise->getGoal()->getDistance() != null) {
    		 	$types[] = 'distance';
    		 }
    		 
    		 if ($exercise->getGoal()->getDuration() != null) {
    		 	$types[] = 'duration';
    		 }
    		 
    		 echo Zend_Json::encode($types);
    	}
    }
    
    public function getTrackPointsAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableView();
    		$trackPoints = $this->workoutService->getTrackPointsByTimestamp($this->_getParam('reportId'), $this->_getParam('timestamp'));
    		
    		$coordinates = array ();
    		foreach ($trackPoints as $trackPoint) {
    			/* @var $trackPoint \Entity\Exercise\TrackPoint */
    			$coordinates[] = array (
    				'lat' => $trackPoint->getLat(),
    				'lon' => $trackPoint->getLon(),
    				'timestamp' => $trackPoint->getTime(),
    			);
    		}
    		
    		echo Zend_Json::encode($coordinates);
    	}
    }
    
    public function getActiveTrackPointsAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableView();
    		$trackPoints = $this->workoutService->getActiveTrackPoints();
    		
    		$coordinates = array ();
    		foreach ($trackPoints as $trackPoint) {
    			/* @var $trackPoint \Entity\Exercise\TrackPoint */
    			$coordinates[] = array (
    				'lat' => $trackPoint->getLat(),
    				'lon' => $trackPoint->getLon(),
    			);
    		}
    		
    		echo Zend_Json::encode($coordinates);
    	}
    }

    public function showSearchFormAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	$this->view->name = $this->_getParam('name');
    	$this->view->sport = $this->workoutService->getSport($this->_getParam('sportId'));
    	$this->view->intensity = $this->_getParam('intensity', null);
    	$this->view->sports = $this->workoutService->getUserSports();
    }
    
    public function showAddWorkoutPlanFormAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	$this->view->sports = $this->workoutService->getUserSports();
    	$this->view->currentUser = $this->userService->getCurrentUser();
    }
   
    /* pievienoja SV - WorkautPlanSet izveles lapa */
    public function showAddWorkoutPlanSetFormAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
  	  $db = Zend_Db_Table::getDefaultAdapter();
  	  $data = $db->fetchAll("SELECT * FROM SetSets order by event_date");
    	$this->view->events = $data;

      $currentUser = $this->userService->getCurrentUser();
      $user_id = $currentUser->getId();

  	  $data = $db->fetchAll("SELECT * FROM UserConfig where user_id=$user_id and param_name='TrainingPlan'");
    	if (isset($data[0])) 
            $this->view->currentPlan = $data[0];
        else 
            $this->view->currentPlan = null;

    	$this->view->currentUser = $this->userService->getCurrentUser();
    }    


    public function trainingAction() {
    	$this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
    	
    	$this->view->workout = $this->workoutService->getWorkout($this->_getParam('id'));
    	$this->view->currentUser = $this->userService->getCurrentUser();
    }

	public function addToMyPlansAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableView();
		}
		
		$this->workoutService->addToMyPlans($this->_getParam('id'));
	}
    
	public function globalAction() {
		$users = $this->userService->getFeaturedUsers();
		shuffle($users);
		
		$randomUsers = array ();
		$c = 0;
		foreach ($users as $user) {
			if ($c > 2) {
				break;
			}
			
			$randomUsers[] = $user;
			$c++;
		}
		
		$this->view->users = $randomUsers;
	}
}