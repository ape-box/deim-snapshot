<?php

class Zend_View_Helper_Evidenced implements Zend_View_Helper_Interface
{
	public function evidenced()
	{
		$model = new estate_models_Ad();
		$this->view->list = $model->listEvidence(config_Registry::get('estate/widgets/evidenced_limit', 6));
		return $this->view->render('estate/views/partials/ad/widget_evidenced.phtml');
	}

	/**
	* @var Zend_View_Interface
	*/
	public $view = null;

	/**
	 * @param  Zend_View_Interface $view
	 * @return Zend_View_Helper_Abstract
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
		return $this;
	}

	/**
	 * @return void
	 */
	public function direct()
	{
		return $this;
	}
}