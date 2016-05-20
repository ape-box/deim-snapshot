<?php

class page_plugins_Acl implements Ape_Plugins_Acl_Interface
{
	public function getResource(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'page')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName())
			{
				case 'view':
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
			if ($request->getModuleName() !== 'page')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName().':'.$request->getActionName())
				{
					case 'view:index':
					case 'admin:index':
					case 'admin:list':
					case 'admin:load-list':
						return 'read';
						break;
					case 'admin:edit':
						return 'update';
						break;
					case 'admin:new':
						return 'create';
						break;
					case 'admin:delete':
					case 'admin:remove':
						return 'delete';
						break;
				}

			throw new Exception('Action or parameter not mapped.');
		}
}