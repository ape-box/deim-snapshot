<?php

if (!defined('APPLICATION_PATH'))		define('APPLICATION_PATH', dirname(__FILE__));
if (!defined('LIBRARY_PATH'))			define('LIBRARY_PATH', APPLICATION_PATH.'/../library');
if (!defined('EXTENDED_LIBRARY_PATH'))	define('EXTENDED_LIBRARY_PATH', LIBRARY_PATH.'/Extended');
if (!defined('ENVIRONMENT'))			define('ENVIRONMENT', 'production');

set_include_path(
	APPLICATION_PATH . '/modules'	. PATH_SEPARATOR .
	APPLICATION_PATH				. PATH_SEPARATOR .
	LIBRARY_PATH					. PATH_SEPARATOR .
	EXTENDED_LIBRARY_PATH			. PATH_SEPARATOR .
	get_include_path()
);

/**
 * Setup the Autoloaders
 * --------------------------------------------------------------------------
 */
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace(array('Ape_', 'Zend_', 'Varien_', 'plugins_'));

if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
	Ape_Benchmark::registerPoint('After Autoloaders registration');

/**
 * Bootstrap the instance
 * ------------------------------------------------------------------
 */
Zend_Controller_Front::getInstance()
	->registerPlugin(new plugins_Bootstrapper(
		new Zend_Config(include APPLICATION_PATH.'/config.php', true),
		APPLICATION_PATH,
		LIBRARY_PATH
), 2);

if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
	Ape_Benchmark::registerPoint('After bootrapper plugin registration');

/* Dispatch the Front Controller */
Zend_Controller_Front::getInstance()->dispatch();

if (defined('DOBENCHMARK') AND DOBENCHMARK === true)
	Ape_Benchmark::registerPoint('After Front dispatch');