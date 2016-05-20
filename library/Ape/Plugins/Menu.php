<?php
class Ape_Plugins_Menu extends Zend_Controller_Plugin_Abstract
{
//    public function routeShutdown(Zend_Controller_Request_Abstract $request)
//    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
//    public function preDispatch(Zend_Controller_Request_Abstract $request)
	public function postDispatch(Zend_Controller_Request_Abstract $request)
		{
			foreach (Zend_Registry::get('config')->modules as $moduleName => $options)
				{
					if (!$options->classes->menu) continue;
					$className = $moduleName . '_plugins_Menu';
					$menu = new $className;
					$resource = Zend_Registry::isRegistered('Zend_Acl') ? Zend_Registry::get('Zend_Acl')->resource : 'frontend';
					switch ($resource)
						{
							case 'frontend':
								Zend_Layout::getMvcInstance()
									->getView()
									->placeholder('defaultMenu')
									->append($menu->renderBlock('defaultMenu', $request));
								break;
							case 'backend':
								Zend_Layout::getMvcInstance()
									->getView()
									->placeholder('adminMenuTop')
									->append($menu->renderBlock('adminMenuTop', $request));
								Zend_Layout::getMvcInstance()
									->getView()
									->placeholder('adminMenuLeft')
									->append($menu->renderBlock('adminMenuLeft', $request));
								break;
						}
				}
		}
}