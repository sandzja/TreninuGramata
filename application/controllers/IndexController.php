<?php

function gzdecode($data){
  $g=tempnam('/tmp','ff');
  @file_put_contents($g,$data);
  ob_start();
  readgzfile($g);
  $d=ob_get_clean();
  return $d;
}

use Service\ServiceManager;
class IndexController extends Zend_Controller_Action {
	
	/**
	 * NewsFeedService
	 * @var \Service\NewsFeed
	 */
	private $newsFeedService;
	
	public function init() {
		$this->newsFeedService = ServiceManager::factory(new \Service\NewsFeed());
    }

    public function indexAction() {

        if (Zend_Auth::getInstance()->hasIdentity()) {
    		$this->_forward('index', 'news-feed');
    	} else {
	    	$this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
			$this->_helper->setLayout('front_page');
			
			$this->view->publicPosts = $this->newsFeedService->getPublicPosts(3);
    	}
    }
    
    public function startAction() {
    	$this->_helper->setLayout('front_page');
    }

}
	
