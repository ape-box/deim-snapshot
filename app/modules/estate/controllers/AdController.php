<?php

class estate_AdController extends Zend_Controller_Action
{
	/**
	 * @param Zend_Paginator $paginator
	 * @return Zend_Paginator
	 */
	protected function _paginatorSetup(Zend_Paginator $paginator)
		{
			$cache = Zend_Cache::factory('Core', 'File', array(
				'lifetime' => 60*1,
				'automatic_serialization' => true
			), array(
				'cache_dir'=>realpath(Zend_Registry::get('config')->file->path->cache)
			));
			$paginator->setCache($cache);
			/**
			 * FUcking Bitch !!! it doubles load times !!!!
			 * -------------------------------------------------------------------------------------
			 */
			$paginator->setCacheEnabled(false);
			$paginator->setItemCountPerPage((integer) config_Registry::get('estate/ad/pagination_items', 12));
			$paginator->setCurrentPageNumber((integer) $this->_getParam('page', 1));
			return $paginator;
		}

	public function listAction()
		{
			tools_models_Breadcrumbs::addCrumb('annunci', '/estate/ad/list');
			$model = new estate_models_Ad();
			$this->view->list = $this->_paginatorSetup($model->listByDate('ASC', 'it_IT'));
		}

	public function detailAction()
		{
			$model = new estate_models_Ad();
			$dv = new Zend_Validate_Digits();
			$id = $this->_getParam('id');
			if(!$dv->isValid($id))
				throw new Exception('Invalid ID');
			$data = $model->find($id, 'it_IT');
			$this->view->data = $data;
			tools_models_Breadcrumbs::addCrumb('annunci', '/estate/ad/list');
			tools_models_Breadcrumbs::addCrumb($data['title'], null);

			if (config_Registry::get('estate/ad/showrequestform', true))
			{
				/* @var $msg Zend_Controller_Action_Helper_SessionMessenger */
				$msg = $this->_helper->sessionMessenger;

				$form = new estate_forms_Request();
				$form->getElement('id')->setValue($id);
				$message = $msg->get('estate/ad/request_status');
				if (!empty($message))
				$form->setDescription(
					$form->getDescription().'<br>'.
					$message
				);
				$this->view->requestform = $form;
			}
		}

	public function searchAction()
		{
			$model = new estate_models_Ad();
			$this->view->list = $this->_paginatorSetup($model->search($this->_getParam('query', ''), 'it_IT'));
			tools_models_Breadcrumbs::addCrumb('annunci', '/estate/ad/list');
			tools_models_Breadcrumbs::addCrumb('Search:', null);
		}

	public function queryAction()
		{
			$form = new estate_forms_AdvancedSearch();
			//if ($this->getRequest()->isPost() AND $form->isValid($this->_getAllParams()))
			if ($form->isValid($this->_getAllParams()))
			{
				$model = new estate_models_Ad();
				$this->view->list = $this->_paginatorSetup($model->query($form->getValues(), 'it_IT'));

				tools_models_Breadcrumbs::addCrumb('annunci', '/estate/ad/list');
				tools_models_Breadcrumbs::addCrumb('Search:', null);
				$this->view->form = $form;
			}
			else return $this->_helper->redirector
				->gotoSimpleAndExit('list', 'ad', 'estate');
		}

	public function requestAction()
		{
			$form = new estate_forms_Request();
			if ($this->getRequest()->isPost() and $form->isValid($this->getRequest()->getParams()))
			{
				$mail = new Zend_Mail('utf-8');
				$this->view->addScriptPath(realpath(dirname(__FILE__).'/../views/partials/ad/'));
				$this->view->assign(array('request'=>$form->getValues()));
				$html = $this->view->render('request.mail.phtml');

				$mail->setBodyHtml($html);
				$mail->addTo(config_Registry::get('application/info/email'));
				$mail->setSubject('deim: info request from website');
				$mail->setFrom(config_Registry::get('application/info/email'));
				/* @var $msg Zend_Controller_Action_Helper_SessionMessenger */
				$msg = $this->_helper->sessionMessenger;
				try {
					$mail->send();
					$msg->add('estate/ad/request_status', '<span style="color: green;">Inviato con successo</span>');
				}
				catch (Exception $e) {
					$msg->add('estate/ad/request_status', '<span style="color: red;">Errore nella spedizione dell\'email</span>');
				}
			}
			$id = $form->getValue('id');

			if ($id > 0)
				return $this->_helper->redirector
					->gotoUrlAndExit($this->view->url(array('action'=>'detail', 'id'=>$id), 'default').'#estate_request_form');
			else
				return $this->_helper->redirector
					->gotoSimpleAndExit('list', 'ad', 'estate', array());
		}
}

