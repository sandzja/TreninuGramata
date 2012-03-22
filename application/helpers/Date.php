<?php
class Helper_Date {

	public static function formatDatabase($date, $format = 'Y-m-d H:i:s') {
		try {
			if ($date == '') {
				return '';
			}
			$date = new DateTime($date);
			$formatted = $date->format($format);
		} catch (Exception $e) {
			return '';
		}
		
		return $formatted;
	}
	
	public static function format(DateTime $date = null, $format = 'd.m.Y H:i') {
		$userTimezone = new DateTimeZone('Europe/Tallinn');
		$gmtTimezone = new DateTimeZone('UTC');
		
		$myDateTime = new DateTime($date->format('Y-m-d H:i:s'), $gmtTimezone);
		$offset = $userTimezone->getOffset($myDateTime);
		
		try {
			if ($date == null) {
				return '';
			}
			
			$formatted = date($format, $myDateTime->format('U') + $offset);
		} catch (Exception $e) {
			return '';
		}
		return $formatted;
	}

}