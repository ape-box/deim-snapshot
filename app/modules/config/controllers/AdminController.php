<?php

class config_AdminController extends Zend_Controller_Action
{
	public function init()
		{
			$this->_helper->getHelper('layout')->setLayout('bootstrap');
		}

	public function indexAction()
		{
			$form = new config_forms_Edit();
			if ($this->getRequest()->isPost() AND $form->isValid($this->_getAllParams()))
			{
					$model = new config_models_Admin();
					$model->save($form->getValues());
					return $this->_helper->redirector
						->gotoSimpleAndExit('index', 'admin', 'config');
			}
			$this->view->form = $form;
		}

}// class

