<?php
interface Ape_Plugins_Acl_Interface
{
	public function getResource(Zend_Controller_Request_Abstract $request);

	public function getPrivilege(Zend_Controller_Request_Abstract $request);
}