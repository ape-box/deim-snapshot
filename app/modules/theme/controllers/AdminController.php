<?php

class theme_AdminController extends Zend_Controller_Action
{
	public function init()
		{
			$this->_helper->getHelper('layout')->setLayout('bootstrap');
		}

	public function indexAction()
	{
		$form = new theme_forms_Layout();
		$filename = Zend_Registry::get('config')->file->path->views . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'front.phtml';

		/*
		if ($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getParam('base', array('text'=>''));
			$data = $data['text'];
			if (!empty($data)) file_put_contents($filename, $data);
		}
		*/

		if (file_exists($filename)) {
			$value = file_get_contents($filename);
			$form->getSubForm('base')->getElement('text')->setValue($value);
		}
		$this->view->form = $form;

		/*
		$filename = Zend_Registry::get('config')->file->path->views . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'front.phtml';
		$this->view->data = file_exists($filename) ? file_get_contents($filename) : '';
		// */
	}

	public function mediaAction()
	{
		;
	}

}// class

