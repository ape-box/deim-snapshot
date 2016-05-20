<?php

class config_Registry
{
	/**
	 * @var config_Registry
	 */
	protected static $_instance;

	/**
	 * @var Zend_Config
	 */
	protected static $_storage;

	/**
	 * @var string
	 */
	protected static $_storage_file;

	/**
	 * @var array
	 */
	protected static $_storage_options = array(
		'allow_modifications'=>false,
		'ignore_constants'=>true
	);

	protected function __construct()
		{
			/**
			 * Setup path to the storage directory
			 *
			 * @var string
			 */
			$_path = Zend_Registry::get('config')->file->path->storage.DIRECTORY_SEPARATOR.__CLASS__;
			if (!file_exists($_path)) mkdir($_path);

			/**
			 * Setup the storage filename and initialize if not present
			 */
			self::$_storage_file = $_path.DIRECTORY_SEPARATOR.'storage.json';
			if (!file_exists(self::$_storage_file))
				{
					$_default_config = new Zend_Config_Json('{}');
					$_f = new Zend_Config_Writer_Json();
					$_f->setConfig($_default_config)
						->setFilename(self::$_storage_file)
						->write();
				}

			/**
			 * Load config
			 */
			self::_loadConfig();
		}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key, $default = null)
		{
			if (!isset(self::$_instance)) self::getInstance();
			return self::$_storage->get($key, $default);
		}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return config_Registry
	 */
	public static function set($key, $value)
		{
			if (!isset(self::$_instance)) self::getInstance();

			/* @var $_conf Zend_Config */
			$_conf = new Zend_Config(self::$_storage->toArray(), true);
			$_conf->__set($key, $value);

			$writer = new Zend_Config_Writer_Json();
			$writer->setConfig($_conf)
				->setFilename(self::$_storage_file)
				->write();

			self::_loadConfig();
			return self::getInstance();
		}

	/**
	 * @return Zend_Config
	 */
	protected function _loadConfig()
		{
			self::$_storage = new Zend_Config_Json(self::$_storage_file, null, self::$_storage_options);
			return self::$_storage;
		}

	/**
	 * @return config_Registry
	 */
	public static function getInstance()
		{
			if (!isset(self::$_instance))
			{
				$classname = __CLASS__;
				self::$_instance = new $classname;
			}
			return self::$_instance;
		}

	/**
	 * Singleton behavioral prevention functions
	 * --------------------------------------------------------------------------------------------------
	 */

	public function __clone()
		{
			throw new Exception('Cannot clone singletons');
		}

	public function __wakeup()
		{
			throw new Exception('Cannot unserialize singletons');
		}
}