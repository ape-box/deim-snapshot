<?php
interface Ape_Plugins_Menu_Interface
{
	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getTopPages(Zend_Controller_Request_Abstract $request);

	/**
	 * @return array|Zend_Navigation_Page|null
	 */
	public function getQuickPages(Zend_Controller_Request_Abstract $request);
}