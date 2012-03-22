<?php

namespace Entity\Feed;

use Doctrine\Common\Collections\ArrayCollection;

use Entity\AbstractEntity;

/**
 * @Entity(repositoryClass="\Repository\Post")
 * @Table(name="FeedPost")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"note" = "\Entity\Feed\Post\Note", "workout" = "\Entity\Feed\Post\Workout", "trainingPlan" = "\Entity\Feed\Post\TrainingPlan", "challenge" = "\Entity\Feed\Post\Challenge", "picture" = "\Entity\Feed\Post\Picture"})
 */
abstract class Post extends AbstractEntity {
	/**
	 * @var integer $id
	 *
	 * @Column(name="id", type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var text $comment
	 *
	 * @Column(name="comment", type="text", nullable=false)
	 */
	protected $comment;

	/**
	 * @var boolean $isPrivate
	 *
	 * @Column(name="is_private", type="boolean", nullable=false)
	 */
	protected $isPrivate;
	
	/**
	 * @var boolean $sendFacebook
	 *
	 * @Column(name="send_facebook", type="boolean", nullable=false)
	 */
	protected $sendFacebook = false;
	
	/**
	 * @var boolean $sendTwitter
	 *
	 * @Column(name="send_twitter", type="boolean", nullable=false)
	 */
	protected $sendTwitter = false;

	/**
	 * @var datetime $dateAdded
	 *
	 * @Column(name="date_added", type="datetime", nullable=false)
	 */
	protected $dateAdded;
	
	/**
	 * @var string $facebookPostId
	 *
	 * @Column(name="facebook_post_id", type="text", nullable=true)
	 */
	protected $facebookPostId;

	/**
	 * @var \Entity\User
	 *
	 * @ManyToOne(targetEntity="Entity\User", inversedBy="posts")
	 * @JoinColumns({
	 *   @JoinColumn(name="author_user_id", referencedColumnName="id")
	 * })
	 */
	protected $author;
	
	/**
	 * @var Doctrine\Common\Collections\ArrayCollection
	 *
	 * @OneToMany(targetEntity="\Entity\Feed\Comment", mappedBy="post", fetch="EXTRA_LAZY")
	 */
	protected $comments;
	
	public function __construct() {
		$this->dateAdded = new \DateTime();
		$this->likes = new ArrayCollection();
		$this->comments = new ArrayCollection();
	}
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set comment
	 *
	 * @param text $comment
	 */
	public function setComment($comment) {
		$this->comment = $comment;
	}

	/**
	 * Get comment
	 *
	 * @return text
	 */
	public function getComment() {
		return $this->comment;
	}

	/**
	 * Set isPrivate
	 *
	 * @param boolean $isPrivate
	 */
	public function setIsPrivate($isPrivate) {
		$this->isPrivate = $isPrivate;
	}

	/**
	 * Get isPrivate
	 *
	 * @return boolean
	 */
	public function isPrivate() {
		return $this->isPrivate;
	}

	/**
	 * @return boolean $sendFacebook
	 */
	public function getSendFacebook() {
		return $this->sendFacebook;
	}

	/**
	 * @param boolean $sendFacebook
	 */
	public function setSendFacebook($sendFacebook) {
		$this->sendFacebook = $sendFacebook;
	}

	/**
	 * @return boolean $sendTwitter
	 */
	public function getSendTwitter() {
		return $this->sendTwitter;
	}
	

	/**
	 * @param boolean $sendTwitter
	 */
	public function setSendTwitter($sendTwitter) {
		$this->sendTwitter = $sendTwitter;
	}
	

	/**
	 * Get dateAdded
	 *
	 * @return datetime
	 */
	public function getDateAdded() {
		return $this->dateAdded;
	}

	/**
	 * @return string $facebookPostId
	 */
	public function getFacebookPostId() {
		return $this->facebookPostId;
	}

	/**
	 * @param string $facebookPostId
	 */
	public function setFacebookPostId($facebookPostId) {
		$this->facebookPostId = $facebookPostId;
	}
	

	/**
	 * Set authorUser
	 *
	 * @param \Entity\User $author
	 */
	public function setAuthor(\Entity\User $author) {
		$this->author = $author;
	}

	/**
	 * Get authorUser
	 *
	 * @return \Entity\User
	 */
	public function getAuthor() {
		return $this->author;
	}
	
	

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getComments() {
		return $this->comments;
	}

	/**
	 * @param \Entity\Feed\Comment $comment
	 */
	public function addComment(\Entity\Feed\Comment $comment) {
		$this->comments[] = $comment;
	}

}