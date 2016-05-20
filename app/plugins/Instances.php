<?php

class plugins_Instances extends Zend_Controller_Plugin_Abstract
{
	protected $_host;

	protected $_namespaces = array();

	/**
	 * @var plugins_models_Instances
	 */
	protected $_model = null;

	public $_actionController = null;

	public function __construct()
		{
		}

	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
		{
			$data = $this->getModel()->getInstanceDataFromHost($this->getHost());
			if (empty($data)) {
				/*
				ksort($_SERVER);
				ksort($_REQUEST);
				$email = new Zend_Mail();
				$email->addTo('alessio.peternelli@gmail.com');
				$email->setSubject('Host non valido.');
				ob_start();
				echo "<pre>\r\n";
				echo __LINE__.'@'.basename(__FILE__)."\r\n";
				echo "Richiesta su host non valido\r\n";
				echo "Host: ".$this->getHost()."\r\n";
				echo "\r\n";
				var_export($_SERVER);
				echo "\r\n\r\n--------------------------------------------------------------------------\r\n\r\n";
				var_export($_REQUEST);
				echo "</pre>";
				$email->setBodyHtml(ob_get_clean());
				ob_end_clean();
				$email->send();
				*/

				/**
				 * +--------------------------------------------------------------------+
				 * | IMPOSTARE UN SITO DI DEFAULT PER GESTIRE GLI HOST NON VALIDI !!!!! |
				 * +====================================================================+
				 */
				$redirect = new Zend_Controller_Response_Http();
				$redirect->setRedirect('http://www.demo-immobiliare.com/', 303);
				Zend_Controller_Front::getInstance()
					->setResponse($redirect);
				$request->setDispatched(true);
				return;
				throw new Exception('Host not valid');
			}

			/* @var $conf Zend_Config */
			$conf = Zend_Registry::get('config');

			/**
			 * Update Database's parameters
			 */
			$conf->database->params->username = $data['dbname'];
			$conf->database->params->dbname   = $data['dbname'];
			$conf->database->params->password = $data['dbpass'];

			/**
			 * Update paths
			 */
			$conf->file->path->storage .= '/'.$this->getNamespaceForCurrentHost();
			$conf->file->path->cache   .= '/'.$this->getNamespaceForCurrentHost();
			$conf->file->path->views   .= '/'.$this->getNamespaceForCurrentHost();

			/**
			 * TODO: Fare filtro globale per la normalizzazione dei PATH
			 */
			if (!file_exists($conf->file->path->storage)) mkdir($conf->file->path->storage);
			if (!file_exists($conf->file->path->cache)) mkdir($conf->file->path->cache);
			if (!file_exists($conf->file->path->views)) mkdir($conf->file->path->views);

			/**
			 * Update default database adapter
			 */
			$dbh = Zend_Db::factory($conf->database);
			Zend_Db_Table_Abstract::setDefaultAdapter($dbh);

			/**
			 * WORK IN PROGRESS
			 * ----------------
			 *
			 * +------------------------------------------------------------------------+
			 * |            Try to redefine base directory for view scripts             |
			 * +------------------------------------------------------------------------+
			 */
			if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer'))
			{
				$view = new Ape_View();
				/* @var $vr Zend_Controller_Action_Helper_ViewRenderer */
				$vr = Zend_Controller_Action_HelperBroker::getHelper('viewRenderer');
				$vr->setView($view);
				Zend_Layout::getMvcInstance()->setView($view);
				$path = $conf->file->path->views . DIRECTORY_SEPARATOR . 'layouts';
				if (file_exists($path))
					Zend_Layout::getMvcInstance()->setLayoutPath($path);
			}
			else die('No View Renderer ?!?!?!');

			/*
			$view = new Ape_View();
			$var = Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer');
			Zend_Debug::dump($var);
			die;
			$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
            Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-80, $viewRenderer);
            Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
            */
		}

	public function getNamespaceForCurrentHost()
	{
		if (!isset($this->_namespaces[$this->getHost()]))
			$this->_namespaces[$this->getHost()] = md5($this->getHost());
		return $this->_namespaces[$this->getHost()];
	}

	/**
	 * @return string|null
	 */
	public function getHost()
	{
		if (empty($this->_host))
			$this->setHost($_SERVER['HTTP_HOST']);
		return $this->_host;
	}

	/**
	 * @param string $host
	 * @return plugins_Instances
	 * @throws Exception
	 */
	public function setHost($host)
	{
		$hv = new Zend_Validate_Hostname();
		if (!$hv->isValid($host)) throw new Exception('Invalid host ?!?!?!');
		$this->_host = $host;
		return $this;
	}

	/**
	 * @return plugins_models_Instances
	 * @throws Exception
	 */
	public function getModel()
	{
		if (!isset($this->_model))
			$this->setModel(new plugins_models_Instances());
		return $this->_model;
	}

	/**
	 * @param plugins_models_Instances $model
	 * @return plugins_models_Instances
	 */
	public function setModel(plugins_models_Instances $model)
	{
		$this->_model = $model;
		return $this->_model;
	}
}

