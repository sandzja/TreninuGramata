<?php
class Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	
	private $acl;
	
	private $noAuthList = array (
		'error.error',
		'user.login',
		'iphone.login',
		'iphone.user',
		'iphone.workout',
		'iphone.exercise',
		'iphone.trackpoint',
		'iphone.friends',
		'iphone.sync',
		'workout.track',
	);
	
	private function createPermissions(Zend_Controller_Request_Abstract $request) {
		$permissions = Zend_Registry::getInstance()->aclList;
		
		$this->acl->addRole(new Zend_Acl_Role(Entity\Role::GUEST));
		foreach (Entity\Role::$roleIds as $name => $id) {
			$this->acl->addRole(new Zend_Acl_Role($name));
		}
		
		if (!in_array($request->getControllerName() . '.' . $request->getActionName(), $this->noAuthList)) {
			
			foreach ($permissions as $controller => $actions) {
				foreach ($actions as $action => $groups) {
					$this->acl->addResource(new Zend_Acl_Resource($controller . '.' . $action));
					foreach ($groups as $group) {
						$this->acl->allow($group, $controller . '.' . $action);
					}
				}
			}
		}
	}
	
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
    	if (!in_array($request->getControllerName() . '.' . $request->getActionName(), $this->noAuthList)) {
	    	if (!Zend_Auth::getInstance()->hasIdentity()) {
	    		if ($request->isXmlHttpRequest()) {
	    			echo '<script>window.location.href="/";</script>';
	    		} else {
	    			$request->setControllerName('index');
	    			$request->setActionName('index');
	    		}
	    	}
    	}
    }
}