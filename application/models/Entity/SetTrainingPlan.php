<?php
namespace Entity;

use Doctrine\Common\Cache\ArrayCache;

use Doctrine\Common\Collections\ArrayCollection;

class SetTrainingPlan extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
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
	 * @Column(type="date")
	 * @var \Date
	*/
	protected $date;
	
	/**
	 * @Column(type="execution_order")
	 * @var int
	 */
	protected $execution_order;

	
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
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param DateTime $date
	 */
	public function setDate($date) {
		$this->date = $date;
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
	 * @return int the $execution_order
	 */
	public function getOrder() {
		return $this->execution_order;
	}

	/**
	 * @param int $execution_order
	 */
	public function setOrder($execution_order) {
		$this->execution_order = $execution_order;
	}

}