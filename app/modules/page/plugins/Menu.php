<?php

class page_plugins_Menu implements Ape_Plugins_Menu_Interface
{
	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		/**
		* "PAGE" pages List and active for detail pages
		*
		* @var Zend_Navigation_Page_Mvc
		*/
		$menu = new Zend_Navigation_Page_Mvc();
		$menu->module = 'page';
		$menu->controller = 'admin';
		$menu->action = 'list';
		$menu->label = 'menutopadmin_page_list_label';
		$menu->title = 'menutopadmin_page_list_title';

		$menu_edit = new Zend_Navigation_Page_Mvc();
		$menu_edit->module = 'page';
		$menu_edit->controller = 'admin';
		$menu_edit->action = 'edit';
		$menu_edit->setVisible(false);

		$menu_new = new Zend_Navigation_Page_Mvc();
		$menu_new->module = 'page';
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
		$item->module = 'page';
		$item->controller = 'admin';
		$item->action = 'new';
		$item->label = 'menuquickadmin_page_new_label';
		$item->title = 'menuquickadmin_page_new_title';
		$menu[] = $item;

		if ($request->getModuleName() == 'page' AND in_array($request->getActionName(), array('edit', 'new')))
		{
			$item = new Zend_Navigation_Page_Uri();
			$item->uri = "javascript:void(0);";
			$item->label = '<hr />';
			$item->title = 'separator';
			$item->setOrder(98);
			$item->setActive(false);
			$menu[] = $item;

			$item = new Zend_Navigation_Page_Mvc();
			$item->action = 'list';
			$item->controller = $request->getControllerName();
			$item->module = $request->getModuleName();
			$item->label = 'menuquickadmin_back_to_list_label';
			$item->title = 'menuquickadmin_back_to_list_title';
			$item->setOrder(99);
			$menu[] = $item;
		}

		return $menu;
	}
}