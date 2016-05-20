<?php

class file_models_File_File implements file_models_Interface
{
	protected $_relPath;
	protected $_absPath;
	protected $_root;
	protected $_cache;
	protected $_name;
	protected $_ext;
	protected $_mime;
	protected $_mtime;

	/**
	 * @param string $path relative path of the file
	 * @param string $root absolute path to directory where files are contained
	 */
	public function __construct($path)
		{
			$this->_relPath = $path;
			$this->_root = realpath(Zend_Registry::get('config')->file->path->storage);
			$this->_cache = Zend_Registry::get('config')->file->path->cache . DIRECTORY_SEPARATOR . 'files';
			if (!file_exists($this->_cache)) mkdir($this->_cache);

			$this->_absPath = $this->_root.$path;

			/**
			 * This is an absolut SHIT !!!!!!!!!!!!!!!!!!!!!!!!!!
			 * -------------------------------------------------------------------------------------------------------
			 */
			if (!file_exists($this->_absPath))
				$this->_absPath = $this->_root.'/files'.$path;


			if (!is_readable($this->_absPath)) {
				/*
				Zend_Controller_Front::getInstance()->getResponse()->setHttpResponseCode(404);
				throw new Zend_Exception("File \"{$this->_relPath}\" is missing", Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE);
				// */
				throw new Zend_Exception("File \"{$this->_relPath}\" is missing");
			}

			$finfo = finfo_open(FILEINFO_MIME);

			$this->_ext  = pathinfo($this->_absPath, PATHINFO_EXTENSION);
			$this->_name = pathinfo($this->_absPath, PATHINFO_FILENAME);
			$this->_mtime = filemtime($this->_absPath);
			$this->_mime =  explode(';', finfo_file($finfo, $this->_absPath));
			$this->_mime = $this->_mime[0];

			switch ($this->_ext) {
				case 'css':
					$this->_mime = 'text/css';
					break;
				default:
					break;
			}

			finfo_close($finfo);
		}

	/**
	 * @return string file name without extension
	 */
	public function getName()
		{
			return $this->_name;
		}

	/**
	 * @return string file extension withot dot (example: "jpg" instead of ".jpg")
	 */
	public function getExt()
		{
			return $this->_ext;
		}

	/**
	 * @return string file name with extension
	 */
	public function getFullName()
		{
			return $this->_name . '.' . $this->_ext;
		}

	/**
	 * @return string file's mime type
	 */
	public function getMime()
		{
			return $this->_mime;
		}

	/**
	 * @return file's modification time timestamp
	 */
	public function getMtime()
		{
			return $this->_mtime;
		}

	/**
	 * @return string file content
	 */
	public function getContent()
		{
			return file_get_contents($this->_absPath);
		}

	/**
	 * @return boolean
	 */
	public function isImage()
		{
			return (bool) exif_imagetype($this->_absPath);
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
			$w = (int) $w;
			$h = (int) $h;
			if ($w < 20) $w = 20;
			elseif ($w > 1920) $w = 1920;
			if ($h < 20) $h = 20;
			elseif ($h > 1920) $h = 1920;

			ini_set('memory_limit', '128M');
			$path = $this->_cache .DIRECTORY_SEPARATOR. 'raw-cache';
			if (!file_exists($path)) mkdir($path);

			$path .= DIRECTORY_SEPARATOR . $w;
			if (!file_exists($path)) mkdir($path);

			$path .= DIRECTORY_SEPARATOR . $h;
			if (!file_exists($path)) mkdir($path);

			$path .= DIRECTORY_SEPARATOR . md5($this->_relPath);

			if (is_readable($path))
				return file_get_contents($path);

			/* @var $img Varien_Image_Adapter_Gd2 */
			$img = Varien_Image_Adapter::factory('GD2');
			$img->open($this->_absPath);
			$img->quality( ($w < 150) && ($h < 150) ? 90 : 70 );
			$img->keepAspectRatio(true);
			$img->keepFrame(true);
			$img->resize($w, $h);

			$img->save($path);
			unset($img);
			return file_get_contents($path);
		}
}