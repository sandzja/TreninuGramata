<?php
namespace Service;


use Service\AbstractService;
class Challenge extends AbstractService {
	
	private $userService;
	
	public function init() {
		$this->userService = new User();
	}

	/**
	 * @param \Entity\User $user
	 * @param int $limit
	 * @param int $offset
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getChallengedUser(\Entity\User $user, $limit, $offset) {
		return $this->em->getRepository('\Entity\Challenge')->findBy(array (
			'opponentUser' => $user->getId(),
		), null, $limit, $offset);
	}
	
	/**
	 * @param \Entity\User $user
	 * @param int $limit
	 * @param int $offset
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getUserChallenged(\Entity\User $user, $limit, $offset) {
		return $this->em->getRepository('\Entity\Challenge')->findBy(array (
			'user' => $user->getId(),
		), null, $limit, $offset);
	}
	
	/**
	 * @param int $limit
	 * @param int $offset
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAllChallenges($limit, $offset) {
		return $this->em->getRepository('\Entity\Challenge')->findBy(array (), null, $limit, $offset);
	}
}


