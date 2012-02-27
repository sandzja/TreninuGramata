<?php

namespace Entity\Feed\Post;

/**
 * Entity\Feed\Picture
 *
 * @Table(name="FeedPicture")
 * @Entity
 */
class Picture extends \Entity\Feed\Post {

	/**
	 * @var string $fileName
	 *
	 * @Column(name="file_name", type="string", length=255, nullable=true)
	 */
	protected $fileName;

	/**
	 * Set fileName
	 *
	 * @param string $fileName
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * Get fileName
	 *
	 * @return string 
	 */
	public function getFileName() {
		return $this->fileName;
	}
}