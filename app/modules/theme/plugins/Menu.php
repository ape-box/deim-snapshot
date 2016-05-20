<?php

class theme_plugins_Menu implements Ape_Plugins_Menu_Interface
{
	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		return null;
	}

	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request)
	{
		if ($request->getModuleName() === 'theme' AND $request->getControllerName() === 'admin')
		{
			$items = array();
/*
			$item = new Zend_Navigation_Page_Mvc();
			$item->module = 'theme';
			$item->controller = 'admin';
			$item->action = 'index';
			$item->label = 'menuquickadmin_theme_index_label';
			$item->title = 'menuquickadmin_theme_index_title';
			$items[] = $item;
*/
			$item = new Zend_Navigation_Page_Mvc();
			$item->module = 'theme';
			$item->controller = 'admin';
			$item->action = 'media';
			$item->label = 'menuquickadmin_theme_media_label';
			$item->title = 'menuquickadmin_theme_media_title';
			$items[] = $item;

			return $items;
		}
		return null;
	}
}