<?php

class estate_forms_Search extends Zend_Form
{
	protected $_elementDecorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
	);

	public function init()
	{
		$this->setAction('/estate/ad/search');
		$this->setMethod('GET');
		$this->setAttrib('class', 'form form-search');
		$this->setDescription('Cerca:');

		$this->addElement('text', 'query', array('class'=>'input-small search-query'));
		$this->addElement('submit', 'search', array('ignore'=>true, 'class'=>'btn'));
	}

	public function loadDefaultDecorators()
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return $this;
		}

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('FormElements')
				->addDecorator('Description', array('placement'=>'prepend'))
				->addDecorator('Form');
		}
		return $this;
	}

	/**
	 * @param $view Zend_View_Interface|null
	 * @return string
	 */
	public function render($view = null)
	{
		if ($view === null) $view = new Zend_View();
		return parent::render($view);
	}
}