<?php

class user_plugins_Menu implements Ape_Plugins_Menu_Interface
{
	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		$menu = new Zend_Navigation_Page_Mvc();
		$menu->module = 'user';
		$menu->controller = 'admin';
		$menu->action = 'index';
		$menu->label = 'users';
		$menu->title = 'users';

		return $menu;
	}

	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request)
	{
		return null;
	}
}