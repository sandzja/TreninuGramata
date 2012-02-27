<?php
/**
 *
 *
 * @author Tõnis Tobre <tobre@webmedia.ee>
 * @copyright Copyright (C) 2008. All rights reserved. Tõnis Tobre
 *
 * Change Log:
 * Date			User		Comment
 * ---------------------------------
 * Feb 16, 2009	tobre	Initial version
 */
class Zend_View_Helper_DateFormat extends Zend_View_Helper_Abstract {

	public function dateFormat($date, $format = 'd.m.Y H:i') {
		if ($date == null || $date == '0000-00-00 00:00:00') {
			return '';
		}
		$dateTime = new DateTime($date);
		
		return $dateTime->format($format);
	}

}
?>