<?php

namespace Entity\Feed;

use Entity\AbstractEntity;

/**
 * Entity\Feed\Like
 *
 * @Table(name="FeedLike")
 * @Entity
 */
class Like extends AbstractEntity {
	
	/**
	 * @var integer $id
	 *
	 * @Column(name="id", type="integer", nullable=false)
	 * @Id
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \Entity\Feed\Post
	 *
	 * @ManyToOne(targetEntity="Entity\Feed\Post")
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
	 *   @JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 */
	protected $user;
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return the \Entity\Feed\Post
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * @param \Entity\Feed\Post $post
	 */
	public function setPost(\Entity\Feed\Post $post) {
		$this->post = $post;
	}

	/**
	 * @return the \Entity\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param \Enitty\User $user
	 */
	public function setUser(\Entity\User $user) {
		$this->user = $user;
	}
	
}