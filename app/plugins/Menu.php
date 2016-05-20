<?php
class plugins_Menu extends Zend_Controller_Plugin_Abstract
{
//	public function routeShutdown(Zend_Controller_Request_Abstract $request)
//	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
//	public function postDispatch(Zend_Controller_Request_Abstract $request)
	public function preDispatch(Zend_Controller_Request_Abstract $request)
		{
			$resource = Zend_Registry::isRegistered('Zend_Acl') ? Zend_Registry::get('Zend_Acl')->resource : 'frontend';
			if ($resource == 'frontend') return;

			$_view = Zend_Layout::getMvcInstance()->getView();
			$navigation_top = new Zend_Navigation_Page_Mvc();
			$navigation_quick = new Zend_Navigation_Page_Mvc();

			foreach (Zend_Registry::get('config')->modules as $moduleName => $options)
				{
					if ($options->classes->menu != 'true') continue;
					$className = $moduleName . '_plugins_Menu';
					/* @var $menu Ape_Plugins_Menu_Interface */
					$menu = new $className;
					switch ($resource)
						{
							case 'backend':
								/**
								 * Navigation Menu TOP
								 */
								$pages = $menu->getTopPages($request);
								if ($pages instanceof Zend_Navigation_Page)
									$navigation_top->addPage($pages);
								elseif (is_array($pages))
									$navigation_top->addPages($pages);
								$pages = $menu->getQuickPages($request);

								/**
								 * Navigation Menu QUICK
								 */
								if ($pages instanceof Zend_Navigation_Page)
									$navigation_quick->addPage($pages);
								elseif (is_array($pages))
									$navigation_quick->addPages($pages);
								break;
						}
				}

			$_view->assign(array(
				'navtop' => $navigation_top,
				'navquick' => $navigation_quick
			));
		}
}