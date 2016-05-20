<?php
class plugins_Acl extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_Acl
	 */
	private $_acl;

	/**
	 * @var Zend_Config
	 */
	private $_config;

	/**
	 * @var array
	 */
	private $_modules_classes = array();

	/**
	 * @var array
	 */
	private $_acl_istances = array();

	/**
	 * @var string
	 */
	private $_user_role = 'guest';

	public function __construct()
		{
			/**
			 * TODO: note sul da farsi
			 *
			 * _/ Chiedere a ogni modulo la lista risorse e verificare l'accesso
			 *  \ Chiedere al modulo richiesto l'accesso alla risorsa
			 *
			 * ? Introdurre uno o pi� livelli di risorse estendento le risorse front e back
			 *   aggiungento come risorsere figlie i controller e a sua volta le azioni ?
			 *
			 * Aggiungere il check a Zend_Auth
			 *
			 * Referenze:
			 * - http://stackoverflow.com/questions/545702/help-with-zend-acl
			 */
			$this->_acl = new Zend_Acl();

			$this->_config = new Zend_Config_Ini('app/acl.ini');

			foreach ($this->_config->roles->toArray() as $role => $parents)
			{
				$parents = empty($parents) ? null : $parents;
				$this->_acl->addRole($role, $parents);
			}

			foreach ($this->_config->resources->toArray() as $resource => $parents)
			{
				$parents = empty($parents) ? null : $parents;
				$this->_acl->addResource($resource, $parents);
			}

			foreach ($this->_config->relation->resources as $resourceName => $resourceMap)
				{
					foreach ($resourceMap->roles as $roleName => $roleMap)
						{
							foreach ($roleMap->privileges as $privilege => $allow)
								{
									$rule = $allow ? 'allow':'deny';
									$this->_acl->{$rule}($roleName, $resourceName, $privilege);
								}
						}
				}

			foreach (Zend_Registry::get('config')->modules as $moduleName => $options)
				{
					if ($options->classes->acl != 'true') continue;
					$className = $moduleName . '_plugins_Acl';
					$this->_modules_classes[$moduleName] = $className;
				}

				if(Zend_Auth::getInstance()->hasIdentity())
				{
					//$this->_user_role = Zend_Auth::getInstance()->getIdentity()->role;
					//throw new Exception('Implementare la verifica d\'identit� !!!!');
					$this->_user_role = 'administrator';
				}
		}

	private function _checkAuth(Zend_Controller_Request_Abstract $request)
		{
			if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
				Ape_Benchmark::registerPoint('ACL Start auth check');

			if (in_array($request->getModuleName(), array_keys($this->_modules_classes)))
				{
					if (in_array($request->getModuleName(), array_keys($this->_acl_istances)))
						{
							$acl = $this->_acl_istances[$request->getModuleName()];
						}
					else
						{
							$acl = new $this->_modules_classes[$request->getModuleName()];
							$this->_acl_istances[$request->getModuleName()] = $acl;
						}

					/* @var $acl Ape_Plugins_Acl_Interface */
					$resource = $acl->getResource($request);
					$privilege = $acl->getPrivilege($request);

					if ($this->_acl->isAllowed($this->_user_role, $resource, $privilege))
						{
							Zend_Registry::set('Zend_Acl', new stdClass());
							Zend_Registry::get('Zend_Acl')->role = $this->_user_role;
							Zend_Registry::get('Zend_Acl')->resource = $resource;
							Zend_Registry::get('Zend_Acl')->privilege = $privilege;
							if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
								Ape_Benchmark::registerPoint('ACL End auth check');
							return true;
						}
					else
						{
							if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
								Ape_Benchmark::registerPoint('ACL End auth check');
							return false;
						}
				}
				if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
					Ape_Benchmark::registerPoint('ACL End auth check');
			return true;
		}

	/**
	 * Faccio un controllo a questo stage
	 * per impedire che l'utente non autorizzato
	 * possa richiedere una pagina protetta
	 */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
		{
			if ( !$this->_checkAuth($request) )
				throw new Zend_Controller_Router_Exception('Access Forbidden', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);
		}

	/**
	 * Faccio un controllo anche a questo stage
	 * per garantire che alcun forward posso accedere
	 * a risorse protette
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
		{
			if ( !$this->_checkAuth($request) )
			{
				Zend_Controller_Front::getInstance()
					->getResponse()
					->clearAllHeaders()
					->clearBody();
				throw new Zend_Controller_Router_Exception('Access Forbidden', Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);
			}
		}
}

