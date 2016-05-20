<?php

class file_plugins_Acl implements Ape_Plugins_Acl_Interface
{
	public function getResource(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'file')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			switch ($request->getControllerName())
			{
				case 'server':
				case 'index':
					return 'frontend';
					break;
				case 'manager':
					return 'backend';
					break;
			}

			throw new Exception('Controller not mapped.');
		}

	public function getPrivilege(Zend_Controller_Request_Abstract $request)
		{
			if ($request->getModuleName() !== 'file')
				throw new Zend_Controller_Router_Exception('Module request mismatch', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);

			if ($request->getControllerName() === 'index' OR $request->getControllerName() === 'server')
				return 'read';

			switch ($request->getControllerName().':'.$request->getActionName())
				{
					case 'manager:upload-file':
					case 'manager:create-directory':
					case 'manager:paste-file':
					case 'manager:paste-directory':
						return 'create';
						break;
					case 'manager:list':
					case 'manager:copy-file':
						return 'read';
						break;
					case 'manager:rename-file':
					case 'manager:rename-directory':
						return 'update';
						break;
					case 'manager:cut-file':
					case 'manager:cut-directory':
					case 'manager:delete-file':
					case 'manager:delete-directory':
						return 'delete';
						break;
				}

			throw new Exception('Action or parameter not mapped.');
		}
}

















