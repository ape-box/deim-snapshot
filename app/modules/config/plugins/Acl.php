<?php

class config_plugins_Acl implements Ape_Plugins_Acl_Interface
{
	public function getResource(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'config')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName())
			{
				case 'admin':
					return 'backend';
					break;
			}

			throw new Exception('Controller not mapped.');
		}

	public function getPrivilege(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'config')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName().':'.$request->getActionName())
				{
					case 'admin:index':
						if ($request->isGet())
							return 'read';
						elseif ($request->isPost())
							return 'update';
						break;
				}

			throw new Exception('Action or parameter not mapped.');
		}
}