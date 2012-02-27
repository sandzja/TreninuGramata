<?php
namespace Service;

use Entity\Feed\Post\Picture;
use Entity\Feed\Comment;
use Entity\Feed\Like;
use Service\DTO\RequestParams;
use Entity\Feed\Post\Note;

class NewsFeed extends AbstractService {
	
	/**
	 * Userservice
	 * @var \Service\User
	 */
	protected $userService;
	protected $workoutService;
	
	public function init() {
		$this->userService = new User();
		$this->workoutService = new \Service\Workout();
	}
	
	/**
	 * Saves note
	 * @param RequestParams $params
	 * @return \Entity\Feed\Post\Note
	 */
	public function saveNote(RequestParams $params) {
		$note = new Note();
		if ($params->userId == null) {
			$note->setAuthor($this->userService->getCurrentUser());
		} else {
			$note->setAuthor($this->userService->getUser($params->userId));
		}
		$note->setComment($params->note);
		$note->setIsPrivate((boolean) $params->isPrivate);
		$note->setSendFacebook((boolean) $params->postFacebook);
		$note->setSendTwitter((boolean) $params->postTwitter);
		
		$this->em->persist($note);
		
		return $note;
	}
	
	public function postFacebook(\Entity\Feed\Post $post = null, $description, $link = null, $linkName = null, $picture = null, \Entity\User $user = null) {
		$facebook = new \Facebook_Graph($this->config->facebook->toArray());
		
		$params = array (
			'message' => stripslashes(htmlspecialchars($description)),
		);
		
		if ($picture != null) {
			$params['picture'] = $picture;
		} else {
		    $params['picture'] = $this->config->meta->domainName . 'gfx/sociallogo.png';
		}
		
		if ($link != null) {
			$params['link'] = $link;
			$params['name'] = $linkName;
		}
		
		if ($user != null) {
			$params['access_token'] = $user->getFacebookAccessToken();
		}
		try {
			$result = $facebook->api('me/feed', 'POST', $params);
		} catch (\Facebook_GraphApiException $e) {
			// just ignore
			return null;
		}
		
		if ($post != null && $post->getSendFacebook()) {
		    $post->setFacebookPostId($result['id']);
		    $this->em->persist($post);
		}
	}
	
	public function commentFacebook(\Entity\Feed\Post $post, $text) {
	    $facebook = new \Facebook_Graph($this->config->facebook->toArray());
	    
	    $params = array (
	    	'message' => stripslashes(htmlspecialchars($text)),
	    );
	    
	    try {
	    	$facebook->api(trim($post->getFacebookPostId()) . '/comments', 'POST', $params);
	    } catch (\Exception $e) {
	    	// just ignore
	    }
	}
	
	public function postTwitter($description) {
		require_once 'Twitter/twitteroauth.php';
		
		$config = $this->config->twitter;
		$twitterSession = new \Zend_session_Namespace('twitter');
		
		$twitteroauth = new \TwitterOAuth($config->consumerKey, $config->consumerSecret, $twitterSession->oauthToken, $twitterSession->oauthSecret);
		$userInfo = $twitteroauth->post('statuses/update', array (
			'status' => substr($description, 0, 140),
		));
	}
	
	public function savePicture(RequestParams $params, $fileName) {
		$fileName = $this->savePictureToDisk($fileName);
		
		$picture = new Picture();
		$picture->setAuthor($this->userService->getCurrentUser());
		$picture->setComment($params->note);
		$picture->setIsPrivate((boolean) $params->isPrivate);
		$picture->setSendFacebook((boolean) $params->postFacebook);
		$picture->setSendTwitter((boolean) $params->postTwitter);
		$picture->setFileName($fileName);
		
		$this->em->persist($picture);
		
		return $picture;
	}
	
	/**
	 * @param \Service\DTO\Workout $workoutDTO
	 * @return \Entity\Feed\Post\Workout
	 */
	public function saveWorkout(\Service\DTO\Workout $workoutDTO) {
		$workout = $this->workoutService->saveWorkout($workoutDTO);
		$trainingPlanReport = $this->workoutService->saveTrainingPlanReport($workout, $workoutDTO);
		$workout->addTrainingPlanReport($trainingPlanReport);
		$exerciseReport = $this->workoutService->saveExerciseReport($workoutDTO, $trainingPlanReport);
		$this->workoutService->saveTrackPointsByCoordinates($workoutDTO, $exerciseReport);
		
		$feedWorkout = new \Entity\Feed\Post\Workout();
		$feedWorkout->setComment($workoutDTO->comment);
		$feedWorkout->setAuthor($this->userService->getCurrentUser());
		$feedWorkout->setIsPrivate($workoutDTO->isPrivate);
		$feedWorkout->setSendFacebook((boolean) $workoutDTO->sendFacebook);
		$feedWorkout->setSendTwitter((boolean) $workoutDTO->sendTwitter);
		$feedWorkout->setWorkout($workout);
		$this->em->persist($feedWorkout);
		
		return $feedWorkout;
	}
	
	/**
	 * @param \Service\DTO\TrainingPlan $trainingPlanDTO
	 * @return \Entity\Feed\Post\TrainingPlan
	 */
	public function saveTrainingPlan(\Service\DTO\TrainingPlan $trainingPlanDTO) {
		$trainingPlan = $this->workoutService->saveTrainingPlan($trainingPlanDTO);
	
		$feedTrainingPlan = new \Entity\Feed\Post\TrainingPlan();
		$feedTrainingPlan->setComment('');
		if ($trainingPlanDTO->userId != null) {
			$user = $this->userService->getUser($trainingPlanDTO->userId);
		} else {
			$user = $this->userService->getCurrentUser();
		}
		$feedTrainingPlan->setAuthor($user);
		$feedTrainingPlan->setIsPrivate($trainingPlanDTO->isPrivate);
		$feedTrainingPlan->setTrainingPlan($trainingPlan);
		$this->em->persist($feedTrainingPlan);
	
		return $feedTrainingPlan;
	}
	
	public function savePictureToDisk($originalName) {
		$originalName = $this->config->filePaths->feedPostPicture . $originalName;

		do {
			$fileName = $this->config->filePaths->feedPostPicture . uniqid(time(), true);
		} while (file_exists($fileName));
		
		rename($originalName, $fileName);
		
		\Helper_Image::resize($fileName, $fileName, 500, 375);
		
		return $fileName;
	}
		

	/**
	 * Gets user posts
	 * @param \Entity\User $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUserPosts($userId = null, $visibility = null, $limit = 10, $offset = 0, array $instances = array ()) {
		if ($userId == null) {
			$user = $this->userService->getCurrentUser();
		} else {
			$user = $this->userService->getUser($userId);
		}
		
		$posts = null;
		
		if ($visibility != null) {
			if ($visibility == 'public') {
				$posts = $this->getPublicPosts($limit, $offset, $instances);
			}
			if ($visibility == 'my') {
				$posts = $this->getOnlyUserPosts($user, $limit, $offset, $instances);
			}
		} else {
			$posts = $this->getFriendsPosts($user, $limit, $offset, $instances);
		}
		
		return $posts;
	}
	
	/**
	 * Gets one user posts
	 * @param int $limit
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getOnlyUserPosts(\Entity\User $user, $limit = null, $offset = null, array $instances = array ()) {
		return $this->em->getRepository('\Entity\Feed\Post')->getOnlyUserPosts($user, $limit, $offset, $instances);
	}
	
	/**
	 * Gets public posts
	 * @param int $limit
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getPublicPosts($limit = null, $offset = null, array $instances = array ()) {
		return $this->em->getRepository('\Entity\Feed\Post')->getPublicPosts($limit, $offset, $instances);
	}
	
	public function getFriendsPosts(\Entity\User $user, $limit = null, $offset = null, array $instances = array ()) {
		return $this->em->getRepository('\Entity\Feed\Post')->getFriendsPosts($user, $limit, $offset, $instances);
	}
	
	
	/**
	 * Gets post by id
	 * @param int $id
	 * @return \Entity\Feed\Post
	 */
	public function getPost($id) {
		return $this->em->find('\Entity\Feed\Post', $id);
	}
	
	
	/**
	 * Adds comment
	 * @param int $postId
	 * @param string $text
	 */
	public function addComment($postId, $text, $userId = null) {
		$post = $this->getPost($postId);
		if ($post != null) {
		    $comment = new Comment();
			if ($userId == null) {
			    $author = $this->userService->getCurrentUser();
			} else {
				$author = $this->userService->getUser($userId);
			}
			$comment->setAuthor($author);
			$comment->setPost($post);
			$comment->setText($text);
			
			$this->em->persist($comment);
			if ($author->getFacebookUserId() != null) {
			    $this->commentFacebook($post, $text);
			}
	    }
	}
	
	public function showPicture($postId) {
		$post = $this->getPost($postId);

		\Helper_Image::show($this->config->filePaths->newsFeedPicture . $post->getFileName());
	}

	public function saveWorkoutPost(\Entity\Feed\Post\Workout $workoutPost) {
		$this->em->persist($workoutPost);
	}
	
	public function saveChallengePost(\Entity\Feed\Post\Challenge $challenge) {
		$this->em->persist($challenge);
	}

	public function getRandomSocialMessage() {
		$messages = $this->config->socialMediaMessages->toArray();
		
		return $messages[mt_rand(0, count($messages) - 1)];
	}

	public function postWorkoutToFacebook(\Entity\Workout $workout, \Entity\User $user = null) {
		$this->postFacebook($workout->getFeedPost(),
			'New entry on my #Trainingbook. ' . $workout->getTrainingPlanReport(0)->getSport()->getName() . ' - ' . round($workout->getDistance() / 1000, 2) .' km. ' . $this->getRandomSocialMessage(),
			$this->config->meta->domainName . 'workout/training/id/' . $workout->getId(),
			'Check it out',
			null,
			$user
		);
	}
	
	public function postWorkoutToTwitter(\Entity\Workout $workout) {
		$this->postTwitter('New entry on my #Trainingbook. ' . $workout->getTrainingPlanReport(0)->getSport()->getName() . ' - ' . round($workout->getDistance() / 1000, 2) .' km.');
	}
	
	public function postLiveTrackingToFacebook(\Entity\Workout $workout, \Entity\User $user = null) {
		$this->postFacebook($workout->getFeedPost(),
			'I\'m out '  . $workout->getTrainingPlanReport(0)->getSport()->getName() . '! Join me!',
			$this->config->meta->domainName . 'workout/track/id/' . $workout->getId(),
			'Watch me live',
			null,
			$user
		);
	}
	
	public function postLiveTrackingToTwitter(\Entity\Workout $workout) {
		$this->postTwitter('I\'m out ' . $workout->getTrainingPlanReport(0)->getSport()->getName() . '! Join me! ' . $this->config->meta->domainName . 'workout/track/id/' . $workout->getId());
	}
}