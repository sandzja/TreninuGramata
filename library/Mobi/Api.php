<?php

class Mobi_Api extends Zend_Rest_Server {
	
	const RESPONSE_OK = 'OK';
	
	public function __construct() {
	    parent::__construct();
 	    set_error_handler(array($this, "error"));
	}
	
	public function returnResponse($flag = null) {
		$this->_returnResponse = true;
	}
	
	protected function _handleStruct($struct) {
		return $struct;
	}
	
	protected function _handleScalar($value) {
		return $value;
	}
	
	public function handle($response = false) {
		parent::handle($response);
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');
	}
	
	public function fault($exception = null, $code = null) {
	    $fault = array (
			'Response' => 'ERROR',
			'ResponseCode' => $code != null ? $code : $exception->getCode(),
			'ResponseMessage' => $exception->getMessage(),
		);
		
		return Zend_Json::encode($fault);
	}
	
	public function error($errno, $errstr, $errfile, $errline) {
	    throw new Zend_Exception($errstr . ' on line ' . $errline . ' in file ' . $errfile, $errno);
	}
}