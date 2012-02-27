<?php

namespace Service;

abstract class AbstractService {
	
	/**
	 * Enity Manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * Config
	 * @var \Zend_Config
	 */
	protected $config;
	
	public function __construct() {
		$doctrine = \Zend_Registry::get('doctrine');
		$this->em = $doctrine->getEntityManager();
		$this->config = \Zend_Registry::getInstance()->config;
		
		$this->init();
	}
	
	public function init() {
		
	}
	
	/**
	 * Gets entity manager
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->em;
	}
	
	public function refreshTransaction() {
		$this->em->flush();
		$this->em->getConnection()->commit();
		$this->em->getConnection()->beginTransaction();
	}
}