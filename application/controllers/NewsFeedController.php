<?php

use Service\DTO\Workout;
use Service\DTO\TrainingPlan;
use Service\ServiceManager;
use Service\DTO\RequestParams;
class NewsFeedController extends Zend_Controller_Action {
	
	/**
	 * @var \Service\NewsFeed
	 */
	private $newsFeedService;
	
	/**
	 * @var \Service\User
	 */
	private $userService;
	
	/**
	 * @var \Service\Workout
	 */
	private $workoutService;
	
	public function init() {
		$this->newsFeedService = ServiceManager::factory(new \Service\NewsFeed());
		$this->userService = ServiceManager::factory(new \Service\User());
		$this->workoutService = ServiceManager::factory(new \Service\Workout());
		
		$this->_helper->setActiveMenu('news-feed');
    }
    
    /**
	 * @var \Service\NewsFeed
	 */
    public function indexAction() {
    	$this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
    	$this->view->type = $this->_getParam('type');
    	$this->view->currentUser = $this->userService->getCurrentUser();
    }
    
    public function postsAction() {
    	if ($this->_request->isXmlHttpRequest()) {
    		$this->_helper->disableLayout();
    	}
    	
    	$this->view->posts = $this->newsFeedService->getUserPosts($this->_getParam('userId'), $this->_getParam('type'), 10, $this->_getParam('page', 0) * 10, array (
	    	'\Entity\Feed\Post\Note',
	    	'\Entity\Feed\Post\Workout',
    		'\Entity\Feed\Post\Picture',
    	));
    	
    	$currentUser = $this->userService->getCurrentUser();
    	if ($this->_getParam('userId') != null && $currentUser->getId() != $this->_getParam('userId')) {
    		$this->_helper->setActiveMenu('friend');
    	}
    	$this->view->currentUser = $currentUser;
    }
    
    public function postAction() {
        if ($this->_getParam('id') != null) {
           $post = $this->newsFeedService->getPost($this->_getParam('id'));
           
           if ($post instanceof \Entity\Feed\Post\Workout) {
               $this->_helper->redirector('training', 'workout', null, array (
               	'id' => $post->getWorkout()->getId(),
               ));
           }
        }
        
        $this->_helper->redirector('index');
    }
    
    public function addNoteAction() {
    	$this->_helper->disableView();
    	if ($this->_request->isPost()) {
    		$note = $this->newsFeedService->saveNote(new RequestParams($this->_request->getParams()));
    		
    		$domainName = Zend_Registry::get('config')->meta->domainName;
    		
	    	if ($this->_getParam('postFacebook') != 0) {
				$this->newsFeedService->postFacebook($note, 'I just added a new note to my #Trainingbook. Check it out!', $domainName . 'user/profile/id/' . $this->userService->getCurrentUser()->getId(), 'Link');
			}
			
			if ($this->_getParam('postTwitter') != 0) {
				$this->newsFeedService->postTwitter('I just added a new note to my #Trainingbook. Check it out!');
			}
    	}
    }
    
    public function addPictureAction() {
    	$form = new Form_Feed_Picture();
    	if ($this->_request->isPost()) {
    		if ($form->isValid($this->_request->getPost())) {
    			$picture = $this->newsFeedService->savePicture(new RequestParams($this->_request->getParams()), $form->picture->getValue());
	    		if ($this->_getParam('postFacebook') != null) {
					$this->newsFeedService->postFacebook($picture, 'I just added a new photo to my #Trainingbook. Check it out!', Zend_Registry::getInstance()->config->meta->domainName . 'user/profile/id/' . $this->userService->getCurrentUser()->getId(), 'Link', Zend_Registry::getInstance()->config->meta->domainName . 'news-feed/show-picture/postId/' . $picture->getId());
				}
				
	    		if ($this->_getParam('postTwitter') != 0) {
					$this->newsFeedService->postTwitter('I just added a new photo to my #Trainingbook. Check it out!');
				}
				
				$this->view->isSuccessful = true;
    		} else {
    			$this->view->isSuccessful = false;
    		}
    	}
    }
    
    public function addWorkoutAction() {
    	$this->_helper->disableView();
   	
    	if ($this->_request->isPost()) {
    		$workoutDTO = new Workout();
    		$workoutDTO->comment = $this->_getParam('comment');
    		$workoutDTO->distance = $this->_getParam('distance');
    		$workoutDTO->unit = $this->_getParam('unit');
    		$workoutDTO->duration = $this->workoutService->calculateDuration($this->_getParam('hours'), $this->_getParam('minutes'), $this->_getParam('seconds'));
    		$workoutDTO->isPrivate = (boolean) $this->_getParam('isPrivate', false);
    		$workoutDTO->sendFacebook = (boolean) $this->_getParam('postFacebook', false);
    		$workoutDTO->sendTwitter = (boolean) $this->_getParam('postTwitter', false);
    		$workoutDTO->location = $this->_getParam('location');
    		$workoutDTO->sportId = $this->_getParam('sportId');
    		$workoutDTO->trainingPlanId = $this->_getParam('trainingPlanId');
    		$workoutDTO->trackPoints = $this->_getParam('trackPoints', array ());
    		$workoutDTO->rating = $this->_getParam('rating');
    		$workoutDTO->calories = $this->_getParam('calories');
    		$workoutDTO->heartRate = $this->_getParam('heartRate');
    		$workoutDTO->pace = $this->_getParam('pace');
    		
    		$workout = $this->newsFeedService->saveWorkout($workoutDTO);
    		
    		
  			if ($this->_getParam('postFacebook') == '1') {
				$this->newsFeedService->postWorkoutToFacebook($workout->getWorkout());
			}
			
    		if ($this->_getParam('postTwitter') != 0) {
				$this->newsFeedService->postWorkoutToTwitter($workout->getWorkout());
			}
			
			$this->_helper->redirector('index');
    	}
    	
    }
 
    /* pievientos no SV - treninu plana izveidosana*/
    public function addTrainingPlanSetAction() {
        $this->_helper->disableView();
        
        if ($this->_request->isPost()) {


        $db = Zend_Db_Table::getDefaultAdapter();
      
        $currentUser = $this->userService->getCurrentUser();
        $user_id = $currentUser->getId();
        $event_id = $this->_getParam('setSetsId');
        $data = $db->fetchCol("SELECT name FROM SetSets where id=?",$event_id);
        $event_name = $data[0];

        $data = $db->fetchCol("SELECT param_key FROM UserConfig where user_id=? and param_name='TrainingPlan'",$user_id);

        // nodzesam visus patreizejos workout
        // nodzesam ieprieksejo planu pec set_id
          if (isset($data[0])) {
            $old_set_id=$data[0];
            //echo $set_id;
            
            error_log("DELETE TrainingPlan, Exercise, Goal,FeedPost,FeedTrainingPlan FROM TrainingPlan INNER JOIN Exercise INNER JOIN Goal INNER JOIN FeedPost INNER JOIN FeedTrainingPlan
           WHERE TrainingPlan.id=Exercise.trainingPlanId AND Exercise.goal_id=Goal.id and FeedTrainingPlan.training_plan_id=TrainingPlan.id and FeedPost.id=FeedTrainingPlan.id
           and TrainingPlan.set_id=$old_set_id");
            
//          $db->query("DELETE TrainingPlan, Exercise, Goal,FeedPost,FeedTrainingPlan FROM TrainingPlan INNER JOIN Exercise INNER JOIN Goal INNER JOIN FeedPost INNER JOIN FeedTrainingPlan
           //WHERE TrainingPlan.id=Exercise.trainingPlanId AND Exercise.goal_id=Goal.id and FeedTrainingPlan.training_plan_id=TrainingPlan.id and FeedPost.id=FeedTrainingPlan.id
           //and TrainingPlan.set_id=$old_set_id");
           
           error_log("after");
        }

                // nodzesam patreizejo, ja ir
        $db->query("delete from UserConfig where user_id=$user_id and param_name='TrainingPlan'");
                // uztaisam jaunu
        $db->query("insert into UserConfig (user_id, param_name, param_value, param_key) values ($user_id,'TrainingPlan','$event_name','$event_id')");

        // liekam ieksaa jaunos
        $data = $db->fetchAll("SELECT * FROM SetTrainingPlan where set_id=?",$event_id);
          foreach ($data as $tp) {
            
          $db->query("insert into TrainingPlan (user_id, sport_id, name, date, execution_order, set_id) values ($user_id,'".$tp['sport_id']."','".$tp['name']."','".$tp['date']."','".$tp['execution_order']."',$event_id)");
          $tp_id = $db->lastInsertId();
          $db->query("INSERT INTO FeedPost (author_user_id,discr) VALUES ('$user_id', 'trainingPlan')");
          $feed_id = $db->lastInsertId();
          $db->query("INSERT INTO FeedTrainingPlan (id, training_plan_id) VALUES ('$feed_id', '$tp_id')");
          
          
          
          $data_ex = $db->fetchAll("SELECT * FROM SetExercise where trainingPlanId=?",$tp['id']);
          foreach ($data_ex as $ex) {
            $db->query("insert into Goal (distance, duration) values ('".$ex['distance']."','".$ex['duration']."')");
            $goal_id = $db->lastInsertId();
            $db->query("insert into Exercise (trainingPlanId, goal_id, name, intensity, note) values ($tp_id, $goal_id, '".$ex['name']."','".$ex['intensity']."','".$ex['note']."')");
          }
            
          }
        

            if ($this->_getParam('postFacebook') != 0) {
                $this->newsFeedService->postFacebook($trainingPlan, 'Just created workout plan on #TrainingBook. Check it out!');
            }
            
        
            if ($this->_getParam('postTwitter') != 0) {
//              $this->newsFeedService->postTwitter($workout);
            }
        
        }
    }

    public function addTrainingPlanAction() {
    	$this->_helper->disableView();
    	
    	if ($this->_request->isPost()) {
    		$trainingPlanDTO = new TrainingPlan();
    		$trainingPlanDTO->isPrivate = $this->_getParam('isPrivate', false);
    		$trainingPlanDTO->sportId = $this->_getParam('sportId');
    		$trainingPlanDTO->name = $this->_getParam('name');
    		
    		$exercisesData = $this->_getParam('exercises');
    		$exercises = array ();
    		$cooldown = null;
    		for ($i = 0; $i < count($exercisesData['type']); $i++) {
    			$exerciseDTO = new \Service\DTO\Exercise();
    			if ($i == 0) {
    				$exerciseDTO->name = 'Warmup';
    			} else if ($i == 1) {
    				$exerciseDTO->name = 'Cooldown';
    			} else {
    				$exerciseDTO->name = 'Exercise ' . $i;
    			}
    			$exerciseDTO->unit = $exercisesData['unit'][$i];
    			$exerciseDTO->note = $exercisesData['note'][$i];
    			$exerciseDTO->goalDistance = $exercisesData['distance'][$i];
    			$exerciseDTO->intensity = $exercisesData['intensity'][$i];
    			$exerciseDTO->goalDuration = ((int) $exercisesData['hours'][$i] * 60 * 60) + ((int) $exercisesData['minutes'][$i] * 60) + ((int) $exercisesData['seconds'][$i]);
    			if ($i == 1) {
    				$cooldown = $exerciseDTO;
    			} else {
    				$trainingPlanDTO->exercises[] = $exerciseDTO;
    			}
    		}
    		$trainingPlanDTO->exercises[] = $cooldown;
			
    		$trainingPlan = $this->newsFeedService->saveTrainingPlan($trainingPlanDTO);
    		
    		if ($this->_getParam('postFacebook') != 0) {
     			$this->newsFeedService->postFacebook($trainingPlan, 'Just created workout plan on #TrainingBook. Check it out!');
    		}
    	
    		if ($this->_getParam('postTwitter') != 0) {
//     			$this->newsFeedService->postTwitter($workout);
    		}
    	
    	}
    }
    
    public function addCommentAction() {
    	$this->_helper->disableView();
    	if ($this->_request->isPost()) {
    		$this->newsFeedService->addComment($this->_getParam('postId'), $this->_getParam('comment'));
    	
//     		$this->_helper->redirector('index');
    	}
    }
    
    public function showPictureAction() {
    	$this->_helper->disableView();
    	$this->newsFeedService->showPicture($this->_getParam('postId'));
    }
    
    public function showNoteFormAction() {
    	$this->_helper->disableLayout();
    }
    
	public function showPhotoFormAction() {
		$this->_helper->disableLayout();
    }
    
	public function showWorkoutFormAction() {
		$this->_helper->disableLayout();
    	$this->view->sports = $this->workoutService->getUserSports();
    	
    	
//    	$this->view->trainingPlans = $this->userService->getCurrentUser()->getTrainingPlans();
    }
    
    public function showCommentsAction() {
    	$this->_helper->disableLayout();
    	
    	$this->view->post = $this->newsFeedService->getPost($this->_getParam('postId'));
    }
}
	
