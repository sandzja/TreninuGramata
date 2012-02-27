<?php

class Zend_View_Helper_SecondsToHours extends Zend_View_Helper_Abstract {

	public function secondsToHours($seconds) {
		$hours = floor($seconds / 60 / 60);
		$minutes = floor(($seconds - ($hours * 60 * 60)) / 60);
		$seconds = floor(($seconds - ($hours * 60 * 60) - ($minutes * 60)));
		
		
		if ($hours < 10) {
			$hours = '0' . $hours;
		}
		
		if ($minutes < 10) {
			$minutes = '0' . $minutes;
		}
		
		if ($seconds < 10) {
			$seconds = '0' . $seconds;
		}
		
		return $hours . ':' . $minutes . ':' . $seconds;
	}

}
?>