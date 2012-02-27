<?php
namespace Entity\Exercise;

use Entity\AbstractEntity;

/**
 * @Entity(repositoryClass="\Repository\TrackPoint")
 */
class TrackPoint extends AbstractEntity {
	
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $alt;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $distanceToLastPoint;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $heart;
	
	/**
	 * @Column(type="integer")
	 * @var int
	 */
	protected $isUploaded;
	
	/**
	 * @Column(type="string")
	 * @var double
	 */
	protected $lat;
	
	/**
	 * @Column(type="string")
	 * @var double
	 */
	protected $lon;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $pulse;
	
	/**
	 * @Column(type="decimal")
	 * @var double
	 */
	protected $speed;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $time;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\Exercise\Report", inversedBy="trackPoints")
	 * @JoinColumn(name="exerciseReportId")
	 * @var Exercise\Report
	 */
	protected $report;
	
	public function __construct() {
		$this->time = new \DateTime();
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
	 * @return string the $alt
	 */
	public function getAlt() {
		return $this->alt;
	}

	/**
	 * @param string $alt
	 */
	public function setAlt($alt) {
		$this->alt = $alt;
	}

	/**
	 * @return string the $distanceToLastPoint
	 */
	public function getDistanceToLastPoint() {
		return $this->distanceToLastPoint;
	}

	/**
	 * @param string $distanceToLastPoint
	 */
	public function setDistanceToLastPoint($distanceToLastPoint) {
		$this->distanceToLastPoint = $distanceToLastPoint;
	}

	/**
	 * @return string the $heart
	 */
	public function getHeart() {
		return $this->heart;
	}

	/**
	 * @param string $heart
	 */
	public function setHeart($heart) {
		$this->heart = $heart;
	}

	/**
	 * @return int the $isUploaded
	 */
	public function getIsUploaded() {
		return $this->isUploaded;
	}

	/**
	 * @param int $isUploaded
	 */
	public function setIsUploaded($isUploaded) {
		$this->isUploaded = $isUploaded;
	}

	/**
	 * @return double the $lat
	 */
	public function getLat() {
		return $this->lat;
	}

	/**
	 * @param double $lat
	 */
	public function setLat($lat) {
		$this->lat = $lat;
	}

	/**
	 * @return double the $lon
	 */
	public function getLon() {
		return $this->lon;
	}

	/**
	 * @param double $lon
	 */
	public function setLon($lon) {
		$this->lon = $lon;
	}

	/**
	 * @return double the $pulse
	 */
	public function getPulse() {
		return $this->pulse;
	}

	/**
	 * @param double $pulse
	 */
	public function setPulse($pulse) {
		$this->pulse = $pulse;
	}

	/**
	 * @return double the $speed
	 */
	public function getSpeed() {
		return $this->speed;
	}

	/**
	 * @param double $speed
	 */
	public function setSpeed($speed) {
		$this->speed = $speed;
	}

	/**
	 * @return DateTime the $time
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @param DateTime $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}
	
	/**
	 * @return Exercise\Report the $report
	 */
	public function getReport() {
		return $this->report;
	}

	/**
	 * @param Exercise\Report $report
	 */
	public function setReport($report) {
		$this->report = $report;
	}


	

}