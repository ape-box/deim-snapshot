<?php
class file_models_File_Clipboard
{
	private $_base_dir;
	private $_cache_dir;

	/**
	 * @var file_models_File_Clipboard
	 */
	static private $_instance;

	/**
	 * @var Zend_Session_Namespace
	 */
	private $_session;

	const CLIPBOARD_SESSION_NAMESPACE = 'ape_filemanager_clipboard_session_namespace';

	private function __construct()
	{
		$this->_base_dir = realpath(Zend_Registry::get('config')->file->path->storage);
		$this->_cache_dir = realpath(Zend_Registry::get('config')->file->path->cache);
	}

	/**
	* @return file_models_File_Clipboard
	*/
	public static function getInstance()
	{
		if (is_null(self::$_instance))
			self::$_instance = new file_models_File_Clipboard();
		return self::$_instance;
	}

	/**
	 * @return Zend_Session_Namespace
	 */
	public function getSession()
	{
		if (is_null($this->_session))
			$this->_session = new Zend_Session_Namespace(self::CLIPBOARD_SESSION_NAMESPACE);
		return $this->_session;
	}

	/**
	 * @return file_models_File_Clipboard
	 */
	public function reserClipboard()
	{
		$this->getSession()->unsetAll();
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClipboard_path()
	{
		return isset($this->getSession()->path) ? $this->getSession()->path : false;
	}

	/**
	 * @return bool
	 */
	public function getClipboard_is_file()
	{
		return isset($this->getSession()->is_file) ? $this->getSession()->is_file : false;
	}

	/**
	 * @return bool
	 */
	public function getClipboard_is_dir()
	{
		return isset($this->getSession()->is_dir) ? $this->getSession()->is_dir : false;
	}

	/**
	 * @return bool
	 */
	public function getClipboard_delete_old()
	{
		return $this->getSession()->delete_old;
	}

	/**
	 * @param string $path
	 * @param bool $delete_old
	 * @throws file_models_File_Exception
	 * @return file_models_File_Clipboard
	 */
	public function setClipboard($path, $delete_old = false)
	{
		if (false === file_exists($path)) throw new file_models_File_Exception('Path does not exists');
		$this->getSession()->unsetAll();
		$this->getSession()->path = $path;
		$this->getSession()->is_file = is_file($path);
		$this->getSession()->is_dir = is_dir($path);
		$this->getSession()->delete_old = $delete_old;
		return $this;
	}
}