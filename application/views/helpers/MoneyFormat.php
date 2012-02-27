<?php
/**
 *
 *
 * @author Tõnis Tobre <tobre@bitweb.ee>
 * @copyright Copyright (C) 2011. All rights reserved. Tõnis Tobre
 *
 * Change Log:
 * Date			User		Comment
 * ---------------------------------
 * Mar 28, 2011	tobre	Initial version
 */
class Zend_View_Helper_MoneyFormat extends Zend_View_Helper_Abstract {

	public function moneyFormat($number, $format = '%i') {
		if(function_exists('money_format')) {
			return money_format($format, $number);
		} else {
			return $number;
		}
	}

}
?>