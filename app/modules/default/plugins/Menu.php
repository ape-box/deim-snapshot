<?php

class default_plugins_Menu implements Ape_Plugins_Menu_Interface
{

	public function getTopPages(Zend_Controller_Request_Abstract $request)
	{
		/**
		 * "HOME" MOMENTANEAMENTE NASCOSTA,
		 * verrà riabilitata quando il software sarà pronto
		 *
		 * @var Zend_Navigation_Page_Mvc
		 */
		/*
		$item = new Zend_Navigation_Page_Uri();
		$item->label = '<i class="icon-home icon-white"></i> Home';
		$item->active = false;
		$item->uri = "javascript:alert('Fare una dashboard introduttiva ?!?!');";
		$item->title = 'Dashboard';
		$menu[] = $item;
		// */

		$item = new Zend_Navigation_Page_Uri();
		$item->active = false;
		$item->uri = "/";
		$item->label = 'menutopadmin_linkt_to_frontend_label';
		$item->title = 'menutopadmin_linkt_to_frontend_title';
		$item->target = '_blank';
		$item->setOrder(99);
		$menu[] = $item;

		return $menu;
	}

	/**
	 * @return Zend_Navigation_Page
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request)
	{
		return null;
	}

}