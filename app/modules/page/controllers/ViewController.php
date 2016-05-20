<?php

class page_ViewController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$model = new page_models_View();
		$dv = new Zend_Validate_Digits();
		$id = $this->_getParam('id');
		if(!$dv->isValid($id))
			throw new Exeption('Invalid ID');
		$this->view->data = $model->find($id, 'it_IT');
	}

}

