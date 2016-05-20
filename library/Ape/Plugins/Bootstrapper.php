<?php

class Ape_Plugins_Bootstrapper extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_Config
	 */
	protected $_config;

	/**
	 * @var Zend_Controller_Front
	 */
	protected $_front;

	/**
	 * @var Zend_Translate_Adapter_Csv
	 */
	protected $_translate;

	/**
	 * @var string Path to application root
	 */
	protected $_path_to_application;

	/**
	 * @var Zend_View
	 */
	protected $_view;

	private $_path_to_library = null;

	/**
	 * @param Zend_Config $config
	 * @param string $appath
	 * @param string $libpath
	 */
	public function __construct(Zend_Config $config, $appath, $libpath)
		{
			$this->_front = Zend_Controller_Front::getInstance();
			$this->_config = $config;
			if (!is_dir($appath)) throw new Exception('Invalid application path');
			if (!is_dir($libpath)) throw new Exception('Invalid library path');
			$this->_path_to_library = $libpath;
			$this->_path_to_application = $appath;
			Zend_Registry::set('config', $this->_config);
			$this->initRoutes();
		}

	/**
	 * @return void
	 */
	public function initRoutes()
		{
			$router = new Zend_Controller_Router_Rewrite();

			$router->addRoute(
				'image',
				new Zend_Controller_Router_Route_Regex(
					'file/image/(\d+)/(\d+)/(.+)',
					array('module' => 'file',
						'controller' => 'index',
						'action' => 'image',
					),
					array(
						1 => 'w',
						2 => 'h',
						3 => 'path'
					),
					'file/image/%d/%d/%s'
				)
			);

			$router->addRoute(
				'thumb',
				new Zend_Controller_Router_Route_Regex(
					'file/thumb/(\d+)/(\d+)/(.+)',
					array('module' => 'file',
						'controller' => 'index',
						'action' => 'image',
					),
					array(
						1 => 'w',
						2 => 'h',
						3 => 'path'
					),
					'file/image/%d/%d/%s'
				)
			);

			$router->addRoute(
				'media',
				new Zend_Controller_Router_Route_Regex(
					'file/media/(\d+)/(\d+)/(\d+)',
					array('module' => 'file',
						'controller' => 'index',
						'action' => 'media'
					),
					array(
						1 => 'id',
						2 => 'w',
						3 => 'h'
					),
					'file/media/%d/%d/%d'
				)
			);

			$router->addRoute(
				'page-view',
				new Zend_Controller_Router_Route(
					'/page/view/*',
					array('module' => 'page',
						'controller' => 'view',
						'action' => 'index'
					)
				)
			);

			$this->_front->setRouter($router);
		}

	/**
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
		{
			$this->initDb();
			$this->initModules();
			$this->initPlugins();
			$this->initTranslations();
		}

	/**
	 * @return void
	 */
	public function initDb()
		{
			if(!isset($this->_config->database)) return;

			$conf = $this->_config->database->toArray();
			$conf = new Zend_Config($conf);
			$dbh = Zend_Db::factory($conf);

			Zend_Db_Table_Abstract::setDefaultAdapter($dbh);
		}

	/**
	 * @return void
	 */
	public function initModules()
		{
			$layout = Zend_Layout::startMvc(array(
				'layoutPath' => $this->_path_to_application.'/layouts',
				'layout' => 'front'
			));

			$this->_view = $layout->getView();
			$this->_view->addScriptPath($this->_path_to_library);
			$this->_view->addScriptPath($this->_path_to_application);
			$this->_view->addScriptPath($this->_path_to_application.'/modules');

			$autoloader = Zend_Loader_Autoloader::getInstance();
			foreach ($this->_config->modules AS $moduleName => $options)
				{
					if ($options->viewhelpers)
						$this->_view->addHelperPath($this->_path_to_application . '/modules/' . $moduleName . '/views/helpers');

					$this->_front->addControllerDirectory($this->_path_to_application . '/modules/' . $moduleName . '/controllers', $moduleName);
					$autoloader->registerNamespace($moduleName);

					if ($options->actionhelpers)
						Zend_Controller_Action_HelperBroker::addPath($this->_path_to_application . '/modules/' . $moduleName . '/controllers/helpers');
				}
		}

	/**
	 * @return void
	 */
	public function initPlugins()
		{
			if(!isset($this->_config->plugins)) return;

			foreach ($this->_config->plugins as $pluginName => $options)
				{
					$this->_front->registerPlugin(new $options->className, $options->stackIndex);
				}
		}

	/**
	 * @return void
	 */
	public function initTranslations()
		{
			$locale = new Zend_Locale(Zend_Registry::get('config')->languages->default);
			$avaibleLocale = Zend_Registry::get('config')->languages->avaible->toArray();
			$locale_to_load = current(
				array_keys(
					array_intersect_key(
						$avaibleLocale,
						$locale->getBrowser()
					)
				)
			);
			if ($locale_to_load) {
				$locale->setDefault($locale_to_load);
				$locale->setLocale($locale_to_load);
			}
			Zend_Registry::set('Zend_Locale', $locale);

			$this->_translate = new Zend_Translate_Adapter_Csv(array(
				'content' => Zend_Registry::get('config')->file->path->translation,
				'delimiter' => ',',
				'locale' => $locale,
				'scan' => Zend_Translate::LOCALE_DIRECTORY
			));
			$this->_translate->setLocale($locale);
			Zend_Registry::set('Zend_Translate', $this->_translate);
			foreach ($this->_config->modules AS $moduleName => $options)
				if ($options->translations)
					$this->_translate->addTranslation(array(
						'content' => $this->_path_to_application . '/modules/' . $moduleName . '/data/translations/',
						'delimiter' => ',',
						'locale' => $locale,
						'scan' => Zend_Translate::LOCALE_DIRECTORY
					));
		}

}
