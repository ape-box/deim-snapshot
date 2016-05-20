<?php

class file_models_Media_File implements file_models_Interface
{
	protected $_params;
	protected $_cache;
	protected $_name;
	protected $_ext;
	protected $_mime;
	protected $_id;
	protected $_data;

	/**
	 * @var file_models_db_MediaFiles
	 */
	protected $_table;

	/**
	 * @param string $path relative path of the file
	 * @param string $root absolute path to directory where files are contained
	 */
	public function __construct($file_id)
		{
			$this->_id = (int) $file_id;
			if ($this->_id == 0) throw new Exception('File does not exists !');

			$this->_table = new file_models_db_MediaFiles();
			$this->_data = $this->_table->findNonBlobFields($this->_id);
			if (empty($this->_data)) throw new Exception('File does not exists !');

			//$this->_cache = realpath(Zend_Registry::get('config')->file->path->cache . DIRECTORY_SEPARATOR . 'media');
			$this->_cache = Zend_Registry::get('config')->file->path->cache . DIRECTORY_SEPARATOR . 'media';
			if (!file_exists($this->_cache)) mkdir($this->_cache);

			$this->_params = (array) json_decode($this->_data['serialized_params']);// im not so sure !!!
			$this->_ext = pathinfo($this->_data['name'], PATHINFO_EXTENSION);
			$this->_name = pathinfo($this->_data['name'], PATHINFO_FILENAME);
			$this->_mime = $this->_data['mime'];
		}

	/**
	 * @return string file id
	 */
	public function getId() {return $this->_id;}

	/**
	 * @return string file checksum
	 */
	public function getChecksum() {return $this->_data['checksum'];}

	/**
	 * @return string file name without extension
	 */
	public function getName() {return $this->_name;}

	/**
	 * @return string file extension withot dot (example: "jpg" instead of ".jpg")
	 */
	public function getExt() {return $this->_ext;}

	/**
	 * @return string file name with extension
	 */
	public function getFullName() {return "{$this->_name}.{$this->_ext}";}

	/**
	 * @return string file's mime type
	 */
	public function getMime() {return $this->_data['mime'];}

	/**
	 * @return file's modification time timestamp
	 */
	public function getMtime() {
		$mtime = isset($this->_params['mtime']) ? $this->_params['mtime'] : time();
		return $mtime;
	}

	/**
	 * @return string file content
	 */
	public function getContent() {
		return $this->_table->findRaw($this->getId());
	}

	/**
	 * @return boolean
	 */
	public function isImage() {
		$is_image = isset($this->_params['is_image']) ? (bool) $this->_params['is_image'] : false;
		return $is_image;
	}

	private function _getCachePath($w = 160, $h = 120)
	{
		$path = $this->_cache . DIRECTORY_SEPARATOR .
				( empty($this->_data['parent_table']) ? '_generic_' : $this->_data['parent_table'] ) . DIRECTORY_SEPARATOR .
				( empty($this->_data['parent_id']) ? '' : $this->_data['parent_id'] . DIRECTORY_SEPARATOR ) .
				"{$w}x{$h}_{$this->_data['checksum']}";
		return $path;
	}

	/**
	 * @param integer $w Larghezza dell'immagine output
	 * @param integer $h Altezza dell'immagine in output
	 * @param string $mime the mime desired for the resulting image
	 * @param bool $watermark
	 * @param bool $crop Indica se dobbiamo centrare e tagliare l'immagine
	 * @return string an image content edited as requested
	 */
	public function editImage($w = 160, $h = 120)
	{
		ini_set('memory_limit', '128M');
		$path = $this->_getCachePath($w, $h);

		if (is_readable($path))
			return file_get_contents($path);

		$directory = pathinfo($path, PATHINFO_DIRNAME);
		if (!is_dir($directory)) mkdir($directory, 0777, true);

		$tmpFile = tempnam($directory, 'tmp');
		file_put_contents($tmpFile, $this->getContent());

		/* @var $img Varien_Image_Adapter_Gd2 */
		$img = Varien_Image_Adapter::factory('GD2');
		$img->open($tmpFile);
		$img->quality( ($w < 160) && ($h < 120) ? 90 : 70 );
		$img->keepAspectRatio(true);
		$img->keepFrame(true);
		$img->resize($w, $h);

		$img->save($path);
		unset($img, $tmpFile);

		return file_get_contents($path);
	}
}