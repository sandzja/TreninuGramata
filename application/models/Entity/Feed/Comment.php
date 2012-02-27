<?php

namespace Entity\Feed;

use Entity\AbstractEntity;

/**
 * Entity\Feedcomment
 *
 * @Table(name="FeedComment")
 * @Entity
 */
class Comment extends AbstractEntity {
	/**
	 * @var integer $id
	 *
	 * @Column(name="id", type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var text $text
	 *
	 * @Column(name="text", type="text", nullable=false)
	 */
	protected $text;

	/**
	 * @var \Entity\Feed\Post
	 *
	 * @ManyToOne(targetEntity="Entity\Feed\post")
	 * @JoinColumns({
	 *   @JoinColumn(name="feed_post_id", referencedColumnName="id")
	 * })
	 */
	protected $post;

	/**
	 * @var \Entity\User
	 *
	 * @ManyToOne(targetEntity="Entity\User")
	 * @JoinColumns({
	 *   @JoinColumn(name="author_user_id", referencedColumnName="id")
	 * })
	 */
	protected $author;

	/**
	 * @var datetime $dateAdded
	 *
	 * @Column(name="date_added", type="datetime", nullable=false)
	 */
	protected $dateAdded;

	public function __construct() {
		$this->dateAdded = new \DateTime();
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
	 * Set text
	 *
	 * @param text $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * Get text
	 *
	 * @return text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Set feedPost
	 *
	 * @param \Entity\Feed\Post $feedPost
	 */
	public function setPost(\Entity\Feed\Post $post) {
		$this->post = $post;
	}

	/**
	 * Get feedPost
	 *
	 * @return \Entity\Feed\Post
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * Set authorUser
	 *
	 * @param \Entity\User $authorUser
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
	 * Get dateAdded
	 *
	 * @return datetime
	 */
	public function getDateAdded() {
		return $this->dateAdded;
	}
}