<?php

class estate_forms_AdvancedSearch extends Zend_Form
{
	protected $_elementDecorators = array(
		array('decorator' => 'ViewHelper', 'options' => array()),
		array('decorator' => 'Label', 'options' => array()),
		array('decorator' => 'HtmlTag', 'options' => array('tag'=>'div'))
	);

	public function init()
	{
		$this->setAction('/estate/ad/query');
		$this->setMethod('GET');// if not the pagination could have problems
		$this->setAttrib('class', 'form form-inline');
		$this->setAttrib('id', 'estate_advanced_search_form');
//		$this->setDescription('Cerca:');
		$spec_fields = new estate_models_Specs();

		$this->addElement('text', 'district', array(
			'filters' => array('StringTrim'),
			'label' => 'Località',
			'style'=>'margin-right: 8px; width: 524px;',
			'decorators' => array(
				array('decorator' => 'ViewHelper', 'options' => array()),
				array('decorator' => 'Label', 'options' => array()),
				array('decorator' => 'HtmlTag', 'options' => array('tag'=>'div', 'style'=>'width: 100%;'))
			)
		));

		$this->addElement('select', 'type', array(
			'filters' => array('StringTrim'),
			'label' => 'Contratto',
			'multiOptions' => $spec_fields->getField('type'),
		));

		$this->addElement('select', 'category', array(
			'filters' => array('StringTrim'),
			'label' => 'Categoria',
			'multiOptions' => $spec_fields->getField('category'),
		));

		$this->addElement('select', 'kind', array(
			'filters' => array('StringTrim'),
			'label' => 'Tipologia',
			'multiOptions' => $spec_fields->getField('kind'),
		));

		$this->addElement('select', 'metrics', array(
			'filters' => array('StringTrim'),
			'label' => 'Superficie',
			'multiOptions' => array(
				''=>'--',
				'0-40'=>'fino a 40',
				'41-60'=>'41-60',
				'61-80'=>'61-80',
				'81-100'=>'81-100',
				'101-0'=>'100+'
			),
		));

		$this->addElement('select', 'garage', array(
			'filters' => array('StringTrim'),
			'label' => 'Garage',
			'multiOptions' => array(
				''=>'--',
				'yes'=>'si',
				'no'=>'no',
			),
		));

		$this->addElement('select', 'price', array(
			'filters' => array('StringTrim'),
			'label' => 'Prezzo',
			'multiOptions' => array(
				''=>'--',
				'0-300'=>'fino a 300',
				'301-500'=>'301-500',
				'501-750'=>'501-750',
				'751-1000'=>'751-1000',
				'1000-0'=>'1000+'
			),
		));

		$this->addElement('submit', 'search', array(
			'ignore'=>true,
			'decorators'=> array(
				'ViewHelper',
				array('HtmlTag', array('tag'=>'div', 'style'=>'float: right;'))
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
				->addDecorator('Description', array('placement'=>'prepend'))
				->addDecorator('Form');
		}
		return $this;
	}
}