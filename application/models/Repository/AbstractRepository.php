<?php

namespace Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository {
	
	public function fetchPairs($column1, $column2, $criteria = array ()) {
		$results = array ();
		foreach ($this->findBy($criteria) as $object) {
			$methodName1 = 'get' . ucfirst($column1);
			$methodName2 = 'get' . ucfirst($column2);
			
			$results[$object->$methodName1()] = $object->$methodName2();
		}
		
		return $results;
	}
	
	protected function getCurrentUser() {
		$currentUser = $this->_em->find('Entity\User', \Zend_Auth::getInstance()->getIdentity()->getId());
		
		return $currentUser;
	}
	
}