<?php
class Zend_View_Helper_ErrorMessages extends Zend_View_Helper_Abstract {
	
	public function errorMessages(Zend_Form $form) {
		$this->view->addScriptPath(APPLICATION_PATH . '/views/partials');
		$this->view->messages = $form->getMessages();
		$this->view->form = $form;
		return $this->view->render('error-messages.phtml');
	}
	
}