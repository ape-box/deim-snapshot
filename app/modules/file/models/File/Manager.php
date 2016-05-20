<?php

class file_models_File_Manager
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
	 * Cache array for ls/scandir results
	 *
	 * @var array
	 */
	private $_scans = array();

	/**
	 * Construct the instance and set base params
	 */
	public function __construct($options = array())
	{
		$this->_base_dir = realpath(Zend_Registry::get('config')->file->path->storage .DIRECTORY_SEPARATOR.'files');
		$this->_cache_dir = realpath(Zend_Registry::get('config')->file->path->cache .DIRECTORY_SEPARATOR.'files');
	}

	/**
	 * Filter input string stripping things like "./", "/$" and resolving "//" to "/"
	 * if strict strip anything else than "A-Za-z0-9_-"
	 *
	 * @param string $string
	 * @param bool $strict
	 * @throws file_models_File_Exception
	 * @return string
	 */
	protected function _filter($string, $strict = false)
	{
		if (PHP_OS !== 'WINNT') {
			if (false === is_string($string)) throw new file_models_File_Exception('$string is not a string');
			$string = substr($string, 0, 1) === DIRECTORY_SEPARATOR ? substr($string, 1) : $string;
			$string = preg_replace('#\.\./#', '', $string);
			$string = str_replace('//', '/', $string);

			if ($strict) {
				$ext = pathinfo($string, PATHINFO_EXTENSION);
				$string = preg_replace('[^A-Za-z0-9_-]', '-', pathinfo($string, PATHINFO_FILENAME));
				$string = trim($string);
				if (!empty($ext)) $string .= ".$ext";
			}

			if (strlen($string) > 254) $string = substr($string, -254);
		}
		return $string;
	}

	/**
	 * Check that folder is a real directory
	 * and filter input string stripping things like "./", "/$" and resolving "//" to "/"
	 *
	 * @param string $folder
	 * @return $string
	 */
	protected function _filterFolder($folder)
	{
		if (PHP_OS !== 'WINNT') {
			$folder = is_string($folder) ? $folder : DIRECTORY_SEPARATOR;
			$folder = substr($folder, 0, 1) === DIRECTORY_SEPARATOR ? $folder : DIRECTORY_SEPARATOR.$folder;
			$folder = preg_replace('#\.\./#', '', $folder);
			$folder = str_replace('//', '/', $folder);
		}
		$folder = is_dir($this->_base_dir.$folder) ? $this->_base_dir.$folder : $this->_base_dir.DIRECTORY_SEPARATOR;
		return $folder;
	}

	/**
	 * Get key for ls/scandire cache storage
	 *
	 * @param string $folder
	 */
	protected function _getKeyForFolder($folder)
	{
		return md5($folder);
	}

	/**
	 * Validate path to directory or file,
	 * absolute or relative
	 * and filter input string stripping things like "./", "/$" and resolving "//" to "/"
	 *
	 * @param string $path
	 * @return bool
	 */
	private function _isValidPath($path)
	{
		if (PHP_OS !== 'WINNT') {
			$path = substr($path, 0, 1) === DIRECTORY_SEPARATOR ? $path : DIRECTORY_SEPARATOR.$path;
			$path = substr($path, -1) === DIRECTORY_SEPARATOR ? substr($path, 0, -1) : $path;
			$path = preg_replace('#\.\./#', '', $path);
			$path = str_replace('//', '/', $path);
			if (substr($path, 0, strlen($this->_base_dir)) !== $this->_base_dir) $path = $this->_base_dir.$path;
			$path = realpath($path);
		}
		return file_exists($path);
	}

	/**
	 * List the content of folder
	 *
	 * @param string $folder
	 * @return array
	 */
	protected function _ls($folder = null)
	{
		$key = $this->_getKeyForFolder($folder);
		if (false === isset($this->_scans[$key]))
		{
			clearstatcache();
			$folder = $this->_filterFolder($folder);
			$list = scandir($folder);
			$this->_scans[$key] = array_slice($list, 2);
		}
		return $this->_scans[$key];
	}

	/**
	 * Generate a correct path from an array of tokens
	 * if check real do a realpath onto the result path
	 *
	 * @param array $tokens
	 * @return string
	 */
	private function _realpath_from_array_tokens(array $tokens, $check_real = true)
	{
		$path = '';
		foreach ($tokens as $e) {
			if (substr($e, 0, 1) === DIRECTORY_SEPARATOR) $e = substr($e, 1);
			if (substr($e, -1) === DIRECTORY_SEPARATOR) $e = substr($e, 0, -1);
			$path .= DIRECTORY_SEPARATOR.$e;
		}
		$path = str_replace('//', '/', $path);
		$path = str_replace('\\\\', '\\', $path);
		if (PHP_OS === 'WINNT') $path = substr($path, 1);
		if ($check_real) $path = realpath($path);
		return $path;
	}

	/**
	 * Put the path to $file in the clipboard,
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @param string $file
	 * @throws file_models_File_Exception
	 * @return file_models_File
	 */
	public function copyFile($folder, $file)
	{
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $file));
		if (false === $this->_isValidPath($path)) throw new file_models_File_Exception('Path does not exists');
		file_models_File_Clipboard::getInstance()->setClipboard($path);
		return $this;
	}

	/**
	 * Do a mkdir in the $folder
	 *
	 * @param string $folder
	 * @param string $name
	 * @throws file_models_File_Exception
	 * @return bool
	 */
	public function createDir($folder, $name)
	{
		$name = $this->_filter($name);
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder));
		if (false === $this->_isValidPath($path)) throw new file_models_File_Exception('Invalid folder');
		if (false !== $this->_realpath_from_array_tokens(array($path, $name)))  throw new file_models_File_Exception('Folder allready exists');
		return mkdir($path.DIRECTORY_SEPARATOR.$name);
	}

	/**
	 * Put the path to $folder in the clipboard
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @throws file_models_File_Exception
	 * @return file_models_File
	 */
	public function cutDir($folder)
	{
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder));
		if (false === $this->_isValidPath($path)) throw new file_models_File_Exception('Path does not exists');
		file_models_File_Clipboard::getInstance()->setClipboard($path, true);
		return $this;
	}

	/**
	 * Puth the path to $file in the cliboard and mark for delete
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @param string $file
	 * @throws file_models_File_Exception
	 * @return file_models_File
	 */
	public function cutFile($folder, $file)
	{
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $file));
		if (false === $this->_isValidPath($path)) throw new file_models_File_Exception('Path does not exists');
		file_models_File_Clipboard::getInstance()->setClipboard($path, true);
		return $this;
	}

	/**
	 * Delete FILES in $folder and then delete $folder
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @throws file_models_File_Exception
	 * @return boolean
	 */
	public function deleteDir($folder)
	{
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder));
		if (false === $this->_isValidPath($path) OR false === is_dir($path)) throw new file_models_File_Exception('Invalid path');

		$flist = $this->listFiles($folder);
		foreach ($flist as $file)
			if (false === $this->deleteFile($folder, $file)) throw new file_models_File_Exception("Failed to dele $file inside $folder");

		file_models_File_Clipboard::getInstance()->reserClipboard();
		return rmdir($path);
	}

	/**
	 * Delete $file in £folder
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @param string $file
	 * @throws file_models_File_Exception
	 * @return boolean
	 */
	public function deleteFile($folder, $file)
	{
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $file));
		if (false === $this->_isValidPath($path) OR false === is_file($path)) throw new file_models_File_Exception('Invalid path');
		file_models_File_Clipboard::getInstance()->reserClipboard();
		return unlink($path);
	}

	/**
	 * Filter $folder and add base dir
	 * (filter input string stripping things like "./", "/$" and resolving "//" to "/" )
	 *
	 * @param string $folder
	 * @return string
	 */
	public function getCurrentFolder($folder)
	{
		$folder = $this->_filterFolder($folder);
		$folder = str_replace($this->_base_dir, '', $folder);
		return $folder;
	}

	/**
	 * Do a scandir in $folder and filter allowing only directories
	 *
	 * @param string $folder
	 * @return array
	 */
	public function listDir($folder = null)
	{
		$list = $this->_ls($folder);
		foreach ($list as $id => $path) {
			$clean_path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $path));
			if (false === is_dir($clean_path)) unset($list[$id]);
		}
		return $list;
	}

	/**
	 * Do a scandir in $folder and filter allowing only files
	 *
	 * @param string $folder
	 * @return array
	 */
	public function listFiles($folder = null)
	{
		$list = $this->_ls($folder);
		foreach ($list as $id => $path) {
			$clean_path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $path));
			if (false === is_file($clean_path)) unset($list[$id]);
		}
		return $list;
	}

	/**
	 * Copy what is in clipboard into $folder
	 *
	 * @param string $folder
	 * @throws file_models_File_Exception
	 * @return file_models_File
	 */
	public function pasteTo($folder)
	{
		$cp = file_models_File_Clipboard::getInstance();

		/**
		 * Destination folder
		 */
		$path_to = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder));
		if (false === $this->_isValidPath($path_to)) throw new file_models_File_Exception('Destination path does not exists');

		/**
		 * Source path
		 */
		$path_from = $cp->getClipboard_path();
		if (false === file_exists($path_from)) throw new file_models_File_Exception('Path from does not exists');

		/**
		 * Destination path
		 */
		$name = array_pop(explode(DIRECTORY_SEPARATOR, $path_from));
		$path_to = $path_to.DIRECTORY_SEPARATOR.$name;
		if (true === file_exists($path_to)) throw new file_models_File_Exception('Path to allready exists');

		switch (true)
		{
			/* didactic case */
			case $cp->getClipboard_is_file():
			case $cp->getClipboard_is_dir():
				if ($cp->getClipboard_delete_old()) rename($path_from, $path_to);
				else copy($path_from, $path_to);
				break;
		}
		$cp->reserClipboard();
		return $this;
	}

	/**
	 * Validate with upload form and movein uploaded file
	 *
	 * @param array $data
	 * @param string $subdir
	 * @param Zend_Form $validation_form
	 * @throws file_models_File_Exception
	 * @return boolean
	 */
	public function registerUpload(array $data, $subdir = null, Zend_Form $validation_form)
	{
		/**
		 * Validate data
		 */
		if($validation_form->isValid($data))
		{
			$tmpFile = $_FILES[file_forms_Upload::FILE_FORM_UPLOAD_FILE_FIELDNAME]['tmp_name'];
			/**
			 * Check for upload errors
			 */
			if (!is_file($tmpFile)) {
				switch ($_FILES[file_forms_Upload::FILE_FORM_UPLOAD_FILE_FIELDNAME]['error']){
						case UPLOAD_ERR_OK: 		throw new file_models_File_Exception('Non ci sono errori, ma il file non esiste');break;
						case UPLOAD_ERR_INI_SIZE:	throw new file_models_File_Exception('Il file è troppo grande rispetto alle impostazioni del server');break;
						case UPLOAD_ERR_FORM_SIZE:	throw new file_models_File_Exception('Il file è troppo grande rispetto alle impostazioni del form');break;
						case UPLOAD_ERR_PARTIAL:	throw new file_models_File_Exception('File incompleto');break;
						case UPLOAD_ERR_NO_FILE:	throw new file_models_File_Exception('Non è stato caricato alcun file');break;
						case UPLOAD_ERR_NO_TMP_DIR:	throw new file_models_File_Exception('Cartella dei temporanei mancante');break;
						case UPLOAD_ERR_CANT_WRITE:	throw new file_models_File_Exception('Impossibile scrivere il file');break;
						case UPLOAD_ERR_EXTENSION:	throw new file_models_File_Exception('Problemi con l\'estensione del file');break;
						default:					throw new file_models_File_Exception('File caricato inesistente!');break;
				}
			}

			/**
			 * Whitelist params
			 *
			 * @var array
			 */
			$params = array();
			$params['name'] = $_FILES[file_forms_Upload::FILE_FORM_UPLOAD_FILE_FIELDNAME]['name'];
			$params['mime'] = $_FILES[file_forms_Upload::FILE_FORM_UPLOAD_FILE_FIELDNAME]['type'];
			$params['size'] = $_FILES[file_forms_Upload::FILE_FORM_UPLOAD_FILE_FIELDNAME]['size'];
			$params['name'] = strtolower($params['name']);

			/**
			 * Filter filename
			 */
			$params['name'] = $this->_filter($params['name'], false);
			if (empty($params['name'])) throw new file_models_File_Exception('Il nome del file non è valido');

			/**
			 * Build and filter folder and build path to new file
			 *
			 * @var string
			 */
			$subdir = preg_replace('#[^A-Za-z0-9 /_-]#', '', $subdir);
			$dir = realpath($this->_base_dir.DIRECTORY_SEPARATOR.$subdir);
			if (substr($dir, -1) !== DIRECTORY_SEPARATOR) $dir .= DIRECTORY_SEPARATOR;
			$params['name'] = $dir . $params['name'];

			/**
			 * Double check folder and file
			 */
			if ( !is_dir($dir) )													throw new file_models_File_Exception('Cartella non valida');
			if ( substr($dir, 0, strlen($this->_base_dir)) !== $this->_base_dir )	throw new file_models_File_Exception('What the fuck!!!');
			if ( file_exists($params['name']))										throw new file_models_File_Exception('Esista già un file con lo stesso nome');

			if(!rename($tmpFile, $params['name']))
				throw new file_models_File_Exception("rename ha fallito di rinominare\r\n$tmpFile\r\n in \r\n{$params['name']}\r\n");
		}
		else return false;

		return true;
	}

	/**
	 * Rename directory $folder to $to
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @param string $to
	 * @throws file_models_File_Exception
	 * @return boolean
	 */
	public function renameDir($folder, $to)
	{
		$name = $this->_filter($to);
		$path = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder));
		if (false === $this->_isValidPath($path)) throw new file_models_File_Exception('Invalid folder');

		$parent = substr($path, -1) === DIRECTORY_SEPARATOR ? substr($path, 0, -1) : $path;
		$parent = explode(DIRECTORY_SEPARATOR, $parent);
		array_pop($parent);
		$parent = implode(DIRECTORY_SEPARATOR, $parent);

		if (false !== $this->_realpath_from_array_tokens(array($parent, $to)))  throw new file_models_File_Exception('Folder allready exists');
		return rename($path, $parent.DIRECTORY_SEPARATOR.$to);
	}

	/**
	 * Rename file $from in $folder to $to
	 * also reset the clipboard (before)
	 *
	 * @param string $folder
	 * @param string $from
	 * @param string $to
	 * @throws file_models_File_Exception
	 * @return bool
	 */
	public function renameFile($folder, $from, $to)
	{
		$from = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder, $from));
		$to = $this->_realpath_from_array_tokens(array($this->_base_dir, $folder)) .DIRECTORY_SEPARATOR. $this->_filter($to);
		if (false === is_file($from)) throw new file_models_File_Exception('Invalid file source');
		if (true === is_file($to)) throw new file_models_File_Exception('Invalid file destination');
		file_models_File_Clipboard::getInstance()->reserClipboard();
		return rename($from, $to);
	}

	/**
	 * Render the upload form
	 * (not sure why i had put it here, maybe tmp solution)
	 * TODO: Check the position of the upload form and his model
	 * @todo: Check the position of the upload form and his model
	 *
	 * @param array $data
	 * @param string $subdir
	 * @return string renderized upload form
	 */
	public function showUploadForm(array $data = array(), $subdir = null)
	{
		$_view = new Zend_View();
		$_view->setScriptPath(dirname(__FILE__) . '/../../views/scripts/manager');
		$_view->form = new file_forms_Upload();
		$data['subfolder'] = $subdir;
		$_view->form->isValid($data);
		return $_view->render('_upload-form.phtml');
	}
}
