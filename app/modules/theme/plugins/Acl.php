<?php

class theme_plugins_Acl implements Ape_Plugins_Acl_Interface
{
	public function getResource(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'theme')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName())
			{
				case 'pub':
					return 'frontend';
					break;
				case 'admin':
					return 'backend';
					break;
			}

			throw new Exception('Controller not mapped.');
		}

	public function getPrivilege(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'theme')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName().':'.$request->getActionName())
				{
					case 'ad:index':
						return 'read';
						break;
					case 'admin:index':
					case 'admin:media':
						if ($request->isPost()) return 'update';
						return 'read';
						break;
					case 'admin:-':
						return 'update';
						break;
					case 'admin:-':
						return 'create';
						break;
					case 'admin:-':
						return 'delete';
						break;
				}

			throw new Exception('Action or parameter not mapped.');
		}
}