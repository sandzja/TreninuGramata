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

    // SV pievienots - action, kas raada current or workout
    public function workoutAction() {
        // mainiigais kas nosaka, vai ir ajax, vai nav
        $this->view->just_workout = false;

        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->disableLayout();
      
            $this->view->just_workout = true;
        }

        $nr = $this->_getParam('nr');

        $workout = $this->nextWorkout($nr);       

        if (isset($workout['treninu_plans'])) {
            $this->view->treninu_plans = $workout['treninu_plans'];
            $this->view->data = $workout['data'];
            $this->view->treninu_workout = $workout['treninu_workout'];
            $this->view->goal_column = $workout['goal_column'];
            $this->view->kopa_html = $workout['kopa_html'];
            $this->view->kopa = $workout['kopa'];
            $this->view->intensity = $workout['intensity'];
        }

    }
    // funkcija ajax next_workout izsaukumam
    public function nextWorkoutAction() {
        $this->_helper->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $nr = $this->_getParam('nr');

        $workout = $this->nextWorkout($nr);       


        $workout_text="";
        $i=0; foreach ($workout['data'] as $row) {
            $class="class=\"sub\"";
            if ($i==0 or $i==count($workout['data'])-1) {$class="";}
            if ($workout['goal_column']=='distance') {
                $goal_html = ($row['distance']/1000)." km";
            } else {
                $goal_html = sec2hms($row['duration']);
            }
            $workout_text.="<p ".$class.">".$goal_html." ".$row['name']." <b>".$workout['intensity'][$row['intensity']]."</b> <span class=\"infoText\"> ".$row['note']."</span></p>";
            $i++; 
        }

        $workout_graph="";
        $i=0; $proc_sum=0; foreach ($workout['data'] as $row) {
            if ($row['intensity']==4) $row['intensity']=0;
          
            $proc = round(($row[$workout['goal_column']]*100/$workout['kopa']),2);
            $proc_sum = $proc_sum + $proc;
            if ($i==count($workout['data'])-1) $proc = $proc + (100-$proc_sum);

            $workout_graph.="<div proc=\"".$proc_sum."\" style=\"width: ".$proc."%;\" class=\"workout0".($row['intensity']+1);
            if ($i==0) $workout_graph.=" first";
            if ($i==count($workout['data'])-1) $workout_graph.=" last";
            $workout_graph.="\">";
            if ($i<>count($workout['data'])-1) { $workout_graph.="<span></span>"; }
            $workout_graph.="</div>";
            $i++; 
        }

        $myArray = array(
                     'workout_name'=> $workout['kopa_html'].' '.$workout['treninu_workout']['name'],
                     'workout_execution_order' => $workout['treninu_workout']['execution_order'].'/'.$workout['treninu_workout']['execution_order_max'],
                     'workout_days_between' => $workout['treninu_workout']['days_between'],
                     'workout_text' => $workout_text,
                     'workout_graph' => $workout_graph,
                   );

        $jsonData = Zend_Json::encode($myArray);
        echo $jsonData;

        //$this->_helper->redirector('workout', 'news-feed', null, array ('nr'=>$this->_getParam('nr')));
    }

    // funkcija next_workout saformesanai
    private function nextWorkout($nr) {

function sec2hms ($sec)
{
    $hms = "";
    $hours = intval(intval($sec) / 3600);
    $hms .= str_pad($hours, 2, "0", STR_PAD_LEFT). ':';
    $minutes = intval(($sec / 60) % 60);
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
    $seconds = intval($sec % 60);
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
}        

        $intensity=array('','Low intensity','Medium intensity','High intensity','');
        $days_between=array('Today','Tomorrow');

        $return_array = array();

        $currentUser = $this->userService->getCurrentUser();
        $db = Zend_Db_Table::getDefaultAdapter();
        $user_id = $currentUser->getId();
        $data = $db->fetchAll("SELECT * FROM UserConfig where user_id=$user_id and param_name='TrainingPlan'");   

        if (isset($data[0])) {
              
            $treninu_plans = $data[0];
            
            if (empty($nr)) {
                $data = $db->fetchAll(
                    "SELECT 
                        Sport.Name sport_name, 
                        TrainingPlan.name name, 
                        TrainingPlan.id, 
                        date_format(TrainingPlan.date,'%W<br>%d %M') date,
                        DATEDIFF( TrainingPlan.date, CURDATE() ) days_between,
                        execution_order 
                    FROM TrainingPlan, Sport 
                    where TrainingPlan.sport_id=Sport.id 
                    and TrainingPlan.user_id=$user_id 
                    and TrainingPlan.set_id=".$treninu_plans['param_key']." 
                    and (date(TrainingPlan.date)=curdate() 
                        or TrainingPlan.date>now()) 
                    order by TrainingPlan.date asc 
                    limit 1");
            } else {
                $data = $db->fetchAll(
                    "SELECT 
                        Sport.Name sport_name, 
                        TrainingPlan.name name, 
                        TrainingPlan.id, 
                        date_format(TrainingPlan.date,'%W<br>%d %M') date,
                        DATEDIFF( TrainingPlan.date, CURDATE() ) days_between,
                        execution_order 
                    FROM TrainingPlan, Sport 
                    where TrainingPlan.sport_id=Sport.id 
                    and TrainingPlan.user_id=$user_id 
                    and TrainingPlan.set_id=".$treninu_plans['param_key']." 
                    and TrainingPlan.execution_order=$nr 
                    ");
            }       
            
            $treninu_workout = $data[0];

            $data = $db->fetchCol("SELECT max(execution_order) FROM TrainingPlan where TrainingPlan.user_id=$user_id and TrainingPlan.set_id=".$treninu_plans['param_key']);
            $treninu_workout['execution_order_max'] = $data[0];            

            $data = $db->fetchCol("SELECT image FROM SetSets where id=".$treninu_plans['param_key']);
            $treninu_workout['image'] = $data[0];            

            if (isset($days_between[$treninu_workout['days_between']])) $treninu_workout['days_between']=$days_between[$treninu_workout['days_between']];
                else $treninu_workout['days_between']=$treninu_workout['date'];
        
            $data = $db->fetchAll("SELECT * FROM Exercise, Goal where  Exercise.goal_id=Goal.id and trainingPlanId = '".$treninu_workout['id']."'");
       
            $goal_column='distance'; if ($data[0]['duration'] <> 0) $goal_column='duration';
              
            //visu sareekinam un samapojam
            $kopa = 0; foreach ($data as $row) $kopa=$kopa+$row[$goal_column];
            if ($goal_column=='distance') $kopa_html = ($kopa/1000)." km"; else $kopa_html = sec2hms($kopa);
      
            $return_array['treninu_plans'] = $treninu_plans;
            $return_array['data'] = $data;
            $return_array['treninu_workout'] = $treninu_workout;
            $return_array['goal_column'] = $goal_column;
            $return_array['kopa_html'] = $kopa_html;
            $return_array['kopa'] = $kopa;
            $return_array['intensity'] = $intensity;

            $return_array['entity'] = $this->workoutService->getTrainingPlan($treninu_workout['id']);

        }
        return $return_array;
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
            
            $delete_data = $db->fetchAll("select * from FeedTrainingPlan where training_plan_id in (SELECT id FROM `TrainingPlan` WHERE user_id=$user_id and set_id=$old_set_id)");
            foreach ($delete_data as $del) {
                $db->query("delete from FeedTrainingPlan where id=".$del['id']);
                $db->query("delete from FeedPost where id=".$del['id']);
            }

            $delete_data = $db->fetchAll("SELECT * FROM Exercise WHERE trainingPlanId in (SELECT id FROM `TrainingPlan` WHERE user_id=$user_id and set_id=$old_set_id)");
            foreach ($delete_data as $del) {
                $db->query("delete from Exercise where id=".$del['id']);
                $db->query("delete from Goal where id=".$del['goal_id']);
            }
            $db->query("delete FROM `TrainingPlan` WHERE user_id=$user_id and set_id=$old_set_id");
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
	
