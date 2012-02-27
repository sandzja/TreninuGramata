<?php

class Zend_View_Helper_Acl extends Zend_View_Helper_Abstract {

	private $currentUser;
	
	public function acl() {
		$doctrine = Zend_Registry::get('doctrine');
		$em = $doctrine->getEntityManager();
		
		$this->currentUser = $em->find('Entity\User', Zend_Auth::getInstance()->getIdentity()->getId());
		
		return $this;
	}
	
	public function isAllowed($resource) {
		try {
			return Zend_Registry::getInstance()->acl->isAllowed($this->currentUser->getCurrentRole()->getRole()->getName(), $resource);
		} catch (Exception $e) {
			// Wont catch exception here
		}
		
	}
}
?>