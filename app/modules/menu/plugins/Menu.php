<?php

class menu_plugins_Menu implements Ape_Plugins_Menu_Interface
{
	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		/**
		* "MENU" items List and active for detail pages
		*
		* @var Zend_Navigation_Page_Mvc
		*/
		$menu = new Zend_Navigation_Page_Mvc();
		$menu->module = 'menu';
		$menu->controller = 'admin';
		$menu->action = 'list';
		$menu->label = 'menutopadmin_menu_list_label';
		$menu->title = 'menutopadmin_menu_list_title';

		$menu_edit = new Zend_Navigation_Page_Mvc();
		$menu_edit->module = 'menu';
		$menu_edit->controller = 'admin';
		$menu_edit->action = 'edit';
		$menu_edit->setVisible(false);

		$menu_new = new Zend_Navigation_Page_Mvc();
		$menu_new->module = 'menu';
		$menu_new->controller = 'admin';
		$menu_new->action = 'new';
		$menu_new->setVisible(false);

		$menu->addPage($menu_edit);
		$menu->addPage($menu_new);

		return $menu;
	}

	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request)
	{
		$item = new Zend_Navigation_Page_Mvc();
		$item->module = 'menu';
		$item->controller = 'admin';
		$item->action = 'list';
		$item->label = 'menuquickadmin_menu_list_label';
		$item->title = 'menuquickadmin_menu_list_title';

		return $item;
	}
}