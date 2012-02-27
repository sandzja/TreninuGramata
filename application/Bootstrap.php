<?php

/**
 * Application bootstrap
 *
 * @uses    Zend_Application_Bootstrap_Bootstrap
 * @package QuickStart
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Bootstrap autoloader for application resources
	 *
	 * @return Zend_Application_Module_Autoloader
	 */
	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => dirname(__FILE__),
		));
		$autoloader->addResourceType('helper', 'helpers', 'Helper');
		return $autoloader;
	}

	public function _initAutoloader()
	{
		require_once 'Doctrine/Common/ClassLoader.php';

		$autoloader = \Zend_Loader_Autoloader::getInstance();

		$bisnaAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
		$autoloader->pushAutoloader(array($bisnaAutoloader, 'loadClass'), 'Bisna');
		
		$doctrineAutoloader = new \Doctrine\Common\ClassLoader('Doctrine');
		$autoloader->pushAutoloader(array($doctrineAutoloader, 'loadClass'), 'Doctrine');

		set_include_path(implode(PATH_SEPARATOR, array(
			APPLICATION_PATH . '/models',
			APPLICATION_PATH,
			get_include_path(),
		)));
		$appAutoloader = new \Doctrine\Common\ClassLoader('Entity');
		$autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Entity');

		$appAutoloader = new \Doctrine\Common\ClassLoader('Repository');
		$autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Repository');
		
		$appAutoloader = new \Doctrine\Common\ClassLoader('Service');
		$autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Service');
	}

	protected function _initFront() {
		$this->bootstrap('config');
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());
		$front->registerPlugin(new Plugin_Auth());
		$front->throwExceptions(false);
// 		date_default_timezone_set('Europe/Tallinn');
 		$locale = new Zend_Locale();
		Zend_Registry::set('Zend_Locale', $locale->getDefault());
	}

	/**
	 * Bootstrap the view doctype
	 *
	 * @return void
	 */
	protected function _initViewSettings()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_TRANSITIONAL');

		$config = Zend_Registry::get('config')->meta;

		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
		$view->headMeta()->appendHttpEquiv('Content-Language', 'en-US');

		$view->headMeta()->appendName('Keywords', $config->defaultKeywords);
		$view->headMeta()->appendName('Description', $config->defaultDescription);
		$view->headMeta()->appendName('Author', 'BitWeb OÜ');
		$view->headMeta('', 'og:description', 'property');
		$view->headLink(array('rel' => 'icon', 'href' => '/favicon.ico', 'type' => 'image/x-icon'));

		$this->view->headLink()->appendStylesheet('/gfx/_styles_screen.css');
		
		$view->headScript()->appendFile('/js/jquery-min.js');
		$view->headScript()->appendFile('/js/jquery.numeric.js');
		$view->headScript()->appendFile('/js/_scripts.js');
		
		$view->headTitle()->append($config->title);
		$view->headTitle()->setSeparator($config->titleSeparator);
		
	}

	protected function _initSession() {
		Zend_Session::start();
	}

	protected function _initConfig() {
		Zend_Registry::set('config', new Zend_Config(require APPLICATION_PATH . '/configs/config.php'));
	}
	
	protected function _initRoutes() {
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();
		
		$router->addRoute('api', new Zend_Controller_Router_Route('/iphone/:version/:action',  array(
	    	'controller' => 'iphone',
	    )));
	}
}
