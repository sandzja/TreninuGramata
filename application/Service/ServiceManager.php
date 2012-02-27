<?php
namespace Service;

use Exception;

class ServiceManager {
	
	private $service;
	private $entityManager;
	private $transactionStarted;
	private static $instance;
	
	private function __construct() {
		$this->transactionStarted = false;
	}
	
	
	/**
	 * Creates servicemanager with current service
	 * @param \Service\AbstractService $service
	 * @return \Service\ServiceManager
	 */
	public static function factory(\Service\AbstractService $service) {
		$serviceManager = new self();
		$serviceManager->setService($service);
		
		return $serviceManager;
	}

	public function setService(\Service\AbstractService $service) {
		$this->transactionStarted = false;
		$this->service = $service;
		if ($this->entityManager == null) {
			$this->entityManager = $service->getEntityManager();
		}
	}
	
	public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager) {
		$this->entityManager = $entityManager;
	}
	
	/**
	 * Enter description here ...
	 * @param string $method
	 * @param array $arguments
	 * @throws \BadMethodCallException
	 * @throws Exception
	 * @return mixed
	 */
	public function __call($method, $arguments) {
		if (!$this->transactionStarted) {
			$this->entityManager->getConnection()->beginTransaction();
			$this->transactionStarted = true;
		}
		$response = null;
		try {
			if (!method_exists($this->service, $method)) {
				throw new \BadMethodCallException('Unable to call method "' . $method . '" from service "' . get_class($this->service) . '"', 500);
			}
			$response = call_user_func_array(array ($this->service, $method), $arguments);
			$this->entityManager->flush();
			$this->entityManager->getConnection()->commit();
		} catch (Exception $e) {
			$this->entityManager->getConnection()->rollback();
			$this->transactionStarted = false;
			throw $e;
		}
		
		$this->transactionStarted = false;
		
		return $response;
	}
}