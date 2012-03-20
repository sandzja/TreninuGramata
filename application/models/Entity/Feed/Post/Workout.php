<?php

namespace Entity\Feed\Post;


/**
 * Entity\Feedworkout
 *
 * @Table(name="FeedWorkout")
 * @Entity
 */
class Workout extends \Entity\Feed\Post {

	/**
	 * @var \Entity\Workout
	 * @OneToOne(targetEntity="Entity\Workout", cascade={"remove"})
	 * @JoinColumn(name="workout_id")
	 */
	protected $workout;
	
	/**
	 * @return \Entity\Workout $workout
	 */
	public function getWorkout() {
		return $this->workout;
	}

	/**
	 * @param \Entity\Workout
	 */
	public function setWorkout(\Entity\Workout $workout) {
		$this->workout = $workout;
	}

	
}