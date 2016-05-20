<?php

class file_models_File_Serving
{
	/**
	 * Base path to store folder
	 *
	 * @var string
	 */
	private $_base_dir;

	/**
	 * Base path to cache folder
	 *
	 * @var string
	 */
	private $_cache_dir;

	/**
	 * Construct the instance and set base params
	 */
	public function __construct($options = array())
	{
		$this->_base_dir = realpath(Zend_Registry::get('config')->file->path->storage);
		$this->_cache_dir = realpath(Zend_Registry::get('config')->file->path->cache);
	}
}