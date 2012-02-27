<?php

namespace Entity\Feed\Post;


/**
 * Entity\Feed\Post\Challenge
 *
 * @Table(name="FeedChallenge")
 * @Entity
 */
class Challenge extends \Entity\Feed\Post {

	/**
	 * @var \Entity\Challenge
	 * @OneToOne(targetEntity="Entity\Challenge")
	 * @JoinColumn(name="challenge_id")
	 */
	protected $challenge;
	
	/**
	 * @return \Entity\Challenge $challenge
	 */
	public function getChallenge() {
		return $this->challenge;
	}

	/**
	 * @param \Entity\Challenge
	 */
	public function setChallenge(\Entity\Challenge $challenge) {
		$this->challenge = $challenge;
	}

	
}