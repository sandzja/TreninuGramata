<?php
class MyTrainingsController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\Workout
	 */
	private $workoutService;
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	public function init() {
		$this->workoutService = \Service\ServiceManager::factory(new \Service\Workout());
		$this->userService = \Service\ServiceManager::factory(new \Service\User());
		$this->_helper->setActiveMenu('my-trainings');
	}
	
	public function indexAction() {
	    $this->view->type = $this->_getParam('type', 'all');
	    $this->view->calendar = $this->_getParam('calendar', 0);
	    $this->view->date = $this->_getParam('date');
	}
	
	public function postsAction() {
	    if ($this->_request->isXmlHttpRequest()) {
	    	$this->_helper->disableLayout();
	    }
	    
	    $type = $this->_getParam('type', 'all');
	    
	    if ($type == 'goals') {
	    	$workouts = $this->workoutService->getGoaledWorkouts($this->userService->getCurrentUser(), 10, $this->_getParam('page', 0) * 10);
	    } else if ($type == 'challenges') {
	    	$workouts = $this->workoutService->getChallengedWorkouts($this->userService->getCurrentUser(), 10, $this->_getParam('page', 0) * 10);
	    } else if ($type == 'records') {
	    	$workouts = $this->workoutService->getRecordedWorkouts($this->userService->getCurrentUser(), 10, $this->_getParam('page', 0) * 10);
	    } else {
	    	$workouts = $this->userService->getCurrentUser()->getWorkouts()->slice($this->_getParam('page', 0) * 10, 10);
	    }
	    
	    $this->view->workouts = $workouts;
	}
	
	public function postsCalendarAction() {
	    $date = DateTime::createFromFormat('Y-m-d', $this->_getParam('date'));
	    if ($date == null) {
	    	$date = new DateTime();
	    }
	    
	    $type = $this->_getParam('type', 'all');
	    if ($type == 'goals') {
	    	$workouts = $this->workoutService->getCalendarGoaledWorkouts($this->userService->getCurrentUser(), $date);
	    } else if ($type == 'challenges') {
	    	$workouts = $this->workoutService->getCalendarChallengedWorkouts($this->userService->getCurrentUser(), $date);
	    } else if ($type == 'records') {
	    	$workouts = $this->workoutService->getCalendarRecordedWorkouts($this->userService->getCurrentUser(), $date);
	    } else {
	    	$workouts = $this->workoutService->getCalendarWorkoutsByUser($this->userService->getCurrentUser(), $date);
	    }
	     
	    $this->view->workouts = $workouts;
	    $this->view->naviDate = clone $date;
	    $this->view->currentMonth = $date->format('M') . ' ' . $date->format('Y');
	    $this->view->nextMonth = $date->modify('+1 month')->format('M') . ' ' . $date->format('Y');
	}
	
	public function graphAction() {
		$this->view->sports = $this->workoutService->getUserSports();
	}
	
	public function graphDailyAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableLayout();
		}
		
		$currentDate = new DateTime();
		if ($this->_getParam('endTime') != null) {
			$currentDate = DateTime::createFromFormat('Y-m-d', $this->_getParam('endTime'));
		}
		
		$this->view->times = $this->workoutService->getDailyWorkoutGraphs($this->userService->getCurrentUser(), $currentDate->format('Y-m-d'), $this->_getParam('sportId'));
		$this->view->currentDate = $currentDate;
	}
	
	public function graphWeeklyAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableLayout();
		}
		
		$currentDate = new DateTime();
		if ($this->_getParam('endTime') != null) {
			$currentDate = DateTime::createFromFormat('Y-m-d', $this->_getParam('endTime'));
		}
		
		$this->view->times = $this->workoutService->getWeeklyWorkoutGraphs($this->userService->getCurrentUser(), $currentDate->format('Y-m-d'), $this->_getParam('sportId'));
		$this->view->currentDate = $currentDate;
	}
	
	public function graphMonthlyAction() {
		if ($this->_request->isXmlHttpRequest()) {
			$this->_helper->disableLayout();
		}
		
		$currentDate = new DateTime();
		if ($this->_getParam('endTime') != null) {
			$currentDate = DateTime::createFromFormat('Y-m-d', $this->_getParam('endTime'));
		}
		
		$this->view->times = $this->workoutService->getMonthlyWorkoutGraphs($this->userService->getCurrentUser(), $currentDate->format('Y-m-d'), $this->_getParam('sportId'));
		$this->view->currentDate = $currentDate;
	}
}