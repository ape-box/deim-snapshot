<?php

class Ape_Object
{
	private $_uniqueid = null;

	public function __construct()
	{
		$this->_uniqueid = $this->_generateUniqueId();
	}

	private function _generateUniqueId()
	{
		return uniqid(get_class($this).'::', true);
	}

	public function getObjectID()
	{
		if (null === $this->_uniqueid)
			$this->_uniqueid = $this->_generateUniqueId();
		return $this->_uniqueid;
	}
}