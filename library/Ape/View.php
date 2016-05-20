<?php

class Ape_View extends Ape_Object implements Zend_View_Interface
{
	/**
	 * @var Zend_View
	 */
	static protected $_view = null;

	public function __construct($tmplPath = null)
	{
		parent::__construct();

		self::$_view === null && self::$_view = new Zend_View();

		if (null !== $tmplPath) {
			$this->setScriptPath($tmplPath);
		}
	}

	/**
	 * @return Zend_View
	 */
	public function getEngine()
	{
		return self::$_view;
	}

	/**
	 * @param string $path The directory to set as the path.
	 * @return void
	 */
	public function setScriptPath($path)
	{
		if (is_readable($path)) {
			$this->getEngine()->setScriptPath($path);
			return;
		}

		throw new Exception('Invalid path provided');
	}

	public function getScriptPaths()
	{
		return $this->getEngine()->getScriptPaths();
	}

	/**
	 * @param string $path
	 * @param string $prefix Unused
	 * @return void
	 */
	public function setBasePath($path, $prefix = 'Zend_View')
	{
		$this->getEngine()->setBasePath($path, $prefix);
		return $this;
	}

	/**
	 * Alias for setScriptPath
	 *
	 * @param string $path
	 * @param string $prefix Unused
	 * @return void
	 */
	public function addBasePath($path, $prefix = 'Zend_View')
	{
		$this->getEngine()->addBasePath($path, $prefix);
		if (false !== stripos($path, Zend_Registry::get('config')->file->path->application))
		{
			$altpath = str_ireplace(Zend_Registry::get('config')->file->path->application,
									Zend_Registry::get('config')->file->path->views,
									$path);
			$altpath = realpath($altpath);
			$this->getEngine()->addBasePath($altpath, $prefix);
		}
		return $this;
	}

	/**
	 * Assign a variable to the template
	 *
	 * @param string $key The variable name.
	 * @param mixed $val The variable value.
	 * @return void
	 */
	public function __set($key, $val)
	{
		$this->getEngine()->assign($key, $val);
	}

	/**
	 * Allows testing with empty() and isset() to work
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return (null !== $this->getEngine()->__isset($key));
	}

	/**
	 * Allows unset() on object properties to work
	 *
	 * @param string $key
	 * @return void
	 */
	public function __unset($key)
	{
		$this->getEngine()->__unset($key);
	}

	public function __call($k, $v)
	{
		/**
		 * SHIT, there is a better way ?
		 * sure ! but what is it ?
		 */
		if (in_array($k, get_class_methods('Zend_View')))
			switch (count($v)) {
				case 0:
					$r = $this->getEngine()->{$k}();
					break;
				case 1:
					$r = $this->getEngine()->{$k}(current($v));
					break;
				case 2:
					$r = $this->getEngine()->{$k}($v[0], $v[1]);
					break;
				case 3:
					$r = $this->getEngine()->{$k}($v[0], $v[1], $v[2]);
					break;
				case 4:
					$r = $this->getEngine()->{$k}($v[0], $v[1], $v[2], $v[3]);
					break;
				case 5:
					$r = $this->getEngine()->{$k}($v[0], $v[1], $v[2], $v[3], $v[4]);
					break;
				default:
					throw new Exception('Unmapped View method '.$k);
					break;
			}
		else $r = $this->getEngine()->__call($k, $v);
		return $r instanceof Zend_View ? $this : $r;
	}

	/**
	 * Assign variables to the template
	 *
	 * Allows setting a specific key to the specified value, OR passing
	 * an array of key => value pairs to set en masse.
	 *
	 * @see __set()
	 * @param string|array $spec The assignment strategy to use (key or
	 * array of key => value pairs)
	 * @param mixed $value (Optional) If assigning a named variable,
	 * use this as the value.
	 * @return void
	 */
	public function assign($spec, $value = null)
	{
		if (is_array($spec)) {
			$this->getEngine()->assign($spec);
			return;
		}

		$this->getEngine()->assign($spec, $value);
	}

	/**
	 * Clear all assigned variables
	 *
	 * Clears all variables assigned to Zend_View either via
	 * {@link assign()} or property overloading
	 * ({@link __get()}/{@link __set()}).
	 *
	 * @return void
	 */
	public function clearVars()
	{
		$this->getEngine()->clearVars();
	}

	/**
	 * Processes a template and returns the output.
	 *
	 * @param string $name The template to process.
	 * @return string The output.
	 */
	public function render($name)
	{
		try {
			return $this->getEngine()->render($name);
		} catch (Exception $e) {
			return '<pre>' .
				$e->getMessage() . "\r\n" .
				$e->getTraceAsString() .
				'</pre>';
		}
	}

}
