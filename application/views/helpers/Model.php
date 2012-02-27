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
class Zend_View_Helper_Model extends Zend_View_Helper_Abstract {

	public function model(Model_Main $model = null) {
		return $model;
	}

}
?>