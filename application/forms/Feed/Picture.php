<?php

class Form_Feed_Picture extends Zend_Form {
	
	public function init() {
		$picture = new Zend_Form_Element_File('picture');
		$picture->setDestination(Zend_Registry::getInstance()->config->filePaths->feedPostPicture);
		$picture->addValidator('Extension', false, 'jpg,png,gif');
		$picture->setRequired();
		$this->addElement($picture);
	}
}