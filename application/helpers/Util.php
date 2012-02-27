<?php
class Helper_Util {

	public static function toAscii($str, $replace = array(), $delimiter = '-') {
		return strtolower(trim(preg_replace(array('~[^0-9a-z]~i', '~-+~'), '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($str, ENT_QUOTES, 'UTF-8'))), '-'));
	}
	
	/**
	 * Returns a random string of the given $length
	 * @param int $length
	 * @return string
	 */
	public static function getRandomString($length) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
			'abcdefghijklmnopqrstuvwxyz0123456789';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		
		return $randomString;
	}
	
	/**
	 * Returns a camelCase version of the dash-separated string.
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function toCamelCase($str) {
		$spaced = str_replace('-', ' ', $str);
		$capitalised = ucwords($spaced);
		$camelCaseStr = str_replace(' ', '', $capitalised);
		
		return $camelCaseStr;
	}
}