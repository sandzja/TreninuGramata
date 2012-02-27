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
class Zend_View_Helper_SubString extends Zend_View_Helper_Abstract {

	public function subString($str, $length, $minword = 3) {
		$sub = '';
		$len = 0;
		
		foreach (explode(' ', $str) as $word) {
			$part = (($sub != '') ? ' ' : '') . $word;
			$sub .= $part;
			$len += strlen($part);
			
			if (strlen($word) > $minword && strlen($sub) >= $length) {
				break;
			}
		}
		
		return $sub . (($len < strlen($str)) ? '...' : '');
	}

}
?>