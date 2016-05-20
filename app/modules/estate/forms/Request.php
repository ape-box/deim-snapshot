<?php

class estate_forms_Request extends Zend_Form
{
	protected $_elementDecorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
		array('decorator' => 'Label', 'options' => array()),
		array('decorator' => 'HtmlTag', 'options' => array('tag'=>'div'))
	);

	public function init()
	{
		$this->setAction('/estate/ad/request');
		$this->setAttrib('class', 'form form-inline');
		$this->setAttrib('id', 'estate_request_form');
		$this->setDescription('Richiedi info/appuntamento:');

		$this->addElement('text', 'referrer', array(
			'filters' => array('StringTrim'),
			'label' => 'Nome e Cognome',
			'decorators' => array(
				array('decorator' => 'ViewHelper', 'options' => array()),
				array('decorator' => 'Label', 'options' => array()),
				array('decorator' => 'HtmlTag', 'options' => array('tag'=>'div'))
			)
		));

		$this->addElement('textarea', 'text', array(
			'filters' => array('StringTrim'),
			'label' => 'Richiesta/Messaggio',
			'cols' => 50,
			'rows' => 10,
			'decorators' => array(
				array('decorator' => 'ViewHelper', 'options' => array()),
				array('decorator' => 'Label', 'options' => array()),
				array('decorator' => 'HtmlTag', 'options' => array('tag'=>'div'))
			)
		));

		$this->addElement('hidden', 'id', array(
			'filters' => array('StringTrim', 'Digits'),
			'decorators' => array(
				array('decorator' => 'ViewHelper', 'options' => array()),
			)
		));

		$this->addElement('submit', 'send', array(
			'ignore'=>true,
			'decorators'=> array(
				'ViewHelper',
				array('HtmlTag', array('tag'=>'div'))
			)
		));
	}

	public function loadDefaultDecorators()
	{
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return $this;
		}

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('FormElements')
				->addDecorator('Description', array('placement'=>'prepend', 'escape'=>false))
				->addDecorator('Form');
		}
		return $this;
	}
}