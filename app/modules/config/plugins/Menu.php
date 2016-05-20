<?php

class config_plugins_Menu implements Ape_Plugins_Menu_Interface
{

	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		/**
		* @var Zend_Navigation_Page_Mvc
		*/
		$item = new Zend_Navigation_Page_Mvc();
		$item->module = 'config';
		$item->controller = 'admin';
		$item->action = 'index';
		$item->label = 'menutopadmin_config_index_label';
		$item->title = 'menutopadmin_config_index_title';

		return $item;
	}

	/**
	 * @return Zend_Navigation_Page
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request)
	{
		return null;
	}

}