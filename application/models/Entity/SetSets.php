<?php
namespace Entity;

use Doctrine\Common\Cache\ArrayCache;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="\Repository\SetSets")
 */
class SetSets extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="setSets")
	 * @JoinColumn(name="coach_id")
	 * @var User
	 */
	protected $user;

	/**
	 * @ManyToOne(targetEntity="\Entity\Sport", inversedBy="setSets")
	 * @JoinColumn(name="sport_id")
	 * @var \Entity\Sport
	 */
	protected $sport;

	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $event;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $distance;

	/**
	 * @Column(type="date")
	 * @var \Date
	*/
	protected $event_date;
	
	/**
	 * @Column(type="integer")
	 * @var int
	 */
	protected $intensity;

	/**
	 * @Column(type="integer")
	 * @var int
	 */
	protected $usage;

	/**
	 * @Column(type="integer")
	 * @var int
	 */
	protected $likes;

	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $image;
	
	public function __construct() {
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return DateTime the $date
	 */
	public function getEventDate() {
		return $this->event_date;
	}

	/**
	 * @param DateTime $date
	 */
	public function setEventDate($date) {
		$this->event_date = $date;
	}
	
	/**
	 * @return User the $user
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * @return \Entity\Sport $sport
	 */
	public function getSport() {
		return $this->sport;
	}

	/**
	 * @param \Entity\Sport $sport
	 */
	public function setSport(\Entity\Sport $sport) {
		$this->sport = $sport;
	}


	/**
	 * @return string the $event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * @param string $event
	 */
	public function setEvent($event) {
		$this->event = $event;
	}


	/**
	 * @return string the $distance
	 */
	public function getDistance() {
		return $this->distance;
	}

	/**
	 * @param string $distance
	 */
	public function setDistance($distance) {
		$this->distance = $distance;
	}

	/**
	 * @return string the $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @return int the $intensity
	 */
	public function getIntensity() {
		return $this->intensity;
	}

	/**
	 * @param int $intensity
	 */
	public function setIntensity($intensity) {
		$this->intensity = $intensity;
	}

	/**
	 * @return int the $usage
	 */
	public function getUsage() {
		return $this->usage;
	}

	/**
	 * @param int $usage
	 */
	public function setUsage($usage) {
		$this->usage = $usage;
	}	

	/**
	 * @return int the $likes
	 */
	public function getLikes() {
		return $this->likes;
	}

	/**
	 * @param int $likes
	 */
	public function setLikes($likes) {
		$this->likes = $likes;
	}	


	protected $details;

	public function getDetails() {
		return $this->details;
	}

	public function SetDetails($details) {
		$this->details = $details;
	}	

}