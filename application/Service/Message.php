<?php
namespace Service;


class Message extends AbstractService {
	
	/**
	 * Userservice
	 * @var \Service\User
	 */
	protected $userService;
	
	public function init() {
		$this->userService = new User();
	}

	public function send($userId, $content) {
		$user = $this->userService->getUser($userId);
		
		$message = new \Entity\Message();
		$message->setAuthorUser($this->userService->getCurrentUser());
		$message->setReceiverUser($user);
		$message->setMessage($content);
		
		$this->em->persist($message);
	}
	
	public function recieve(\Entity\User $user, \DateTime $timestamp) {
		return $this->em->getRepository('\Entity\Message')->getMessages($user, $timestamp);
	}
}