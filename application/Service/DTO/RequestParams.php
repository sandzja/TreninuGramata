<?php
namespace Service\DTO;

class RequestParams {
	
	private $params;
	
	public function __construct(array $params = array ()) {
		$this->params = $params;
	}
	
	public function __get($param) {
		if (!isset($this->params[$param])) {
			return null;
		}
		
		return $this->params[$param];
	}
	
}