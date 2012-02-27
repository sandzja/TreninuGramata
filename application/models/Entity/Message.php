<?php

namespace Entity;

/**
 * @Entity(repositoryClass="\Repository\Message")
 */
class Message extends AbstractEntity {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 * @var int
	 */
	protected $id;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="sentMessages")
	 * @JoinColumn(name="author_user_id")
	 * @var \Entity\User
	 */
	protected $authorUser;
	
	/**
	 * @ManyToOne(targetEntity="\Entity\User", inversedBy="receivedMessages")
	 * @JoinColumn(name="receiver_user_id")
	 * @var \Entity\User
	 */
	protected $receiverUser;
	
	/**
	 * @Column(type="string")
	 * @var string
	 */
	protected $message;
	
	/**
	 * @Column(name="date_sent", type="datetime")
	 * @var DateTime
	 */
	protected $dateSent;
	
	public function __construct() {
		$this->dateSent = new \DateTime();
	}
	
	/**
	 * @return int the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return \Entity\User $authorUser
	 */
	public function getAuthorUser() {
		return $this->authorUser;
	}
	

	/**
	 * @param \Entity\User $authorUser
	 */
	public function setAuthorUser($authorUser) {
		$this->authorUser = $authorUser;
	}
	

	/**
	 * @return \Entity\User $receiverUser
	 */
	public function getReceiverUser() {
		return $this->receiverUser;
	}
	

	/**
	 * @param \Entity\User $receiverUser
	 */
	public function setReceiverUser($receiverUser) {
		$this->receiverUser = $receiverUser;
	}
	

	/**
	 * @return string $message
	 */
	public function getMessage() {
		return $this->message;
	}
	

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}
	

	/**
	 * @return \Entity\DateTime $dateSent
	 */
	public function getDateSent() {
		return $this->dateSent;
	}
	

	/**
	 * @param \Entity\DateTime $dateSent
	 */
	public function setDateSent($dateSent) {
		$this->dateSent = $dateSent;
	}


	
}