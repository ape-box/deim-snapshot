<?php

class estate_forms_New extends Ape_Form
{
	public function init()
		{
			$this->addSubForm(new Ape_Form_SubForm(), 'base', 10);
			$this->getSubForm('base')->setLegend('estate_base_data');
			$spec_fields = new estate_models_Specs();

			$this->getSubForm('base')->addElement('text', 'title', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'estate_title',
				'class' => 'span12',
				'placeholder' => 'Post modern loft in Union Square NY',
				'decorators' => $this->_big_element_decorators
			));

			$this->getSubForm('base')->addElement('text', 'ad_code', array(
				'filters' => array('StringTrim'),
				'required' => false,
				'label' => 'estate_ad_code',
				'class' => 'span12',
				'placeholder' => 'rent-075',
				'decorators' => $this->_small_element_decorators
			));

			$date_validator = new Zend_Validate_Regex('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/');
			$date_validator->setMessage("'%value%' is not a valid date in format dd/mm/yyyy", Zend_Validate_Regex::NOT_MATCH);
			$this->getSubForm('base')->addElement('text', 'date', array(
				'filters' => array('StringTrim', array('PregReplace', array('/^([0-9]{4})\-([0-9]{2})\-([0-9]{2}).*$/', '$3/$2/$1'))),
				'validators' => array($date_validator),
				'required' => true,
				'class' => 'datepicker span12',
				'label' => 'estate_date',
				'value' => date('d/m/Y'),
				'decorators' => $this->_small_element_decorators
			));

			$this->getSubForm('base')->addElement('checkbox', 'pub', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_pub',
				'description' => 'estate_show_ad',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('base')->addElement('checkbox', 'evidence', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_evidence',
				'description' => 'estate_show_ad_in_evidence',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('base')->addElement('textarea', 'text', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'label' => 'estate_text',
				'class' => 'tiny span12',
				'cols' => 20,
				'rows' => 10,
				'decorators' => $this->_textarea_decorators
			));

			$this->getSubForm('base')->addElement('button', 'submit', array(
				'required' => false,
				'ignore' => true,
				'type' => 'submit',
				'class' => 'btn btn-primary',
				'label' => 'estate_ad-save',
				'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
			));

			$this->getSubForm('base')->addDisplayGroup(array('title', 'ad_code', 'date', 'pub', 'evidence'), 'row-1', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 10
			));

			$this->getSubForm('base')->addDisplayGroup(array('text'), 'row-2', array(
				'decorators' => array(
					'FormElements',
				),
				'order' => 20
			));

			$this->getSubForm('base')->addDisplayGroup(array('submit'), 'row-3', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'form-actions'))
				),
				'order' => 30
			));

			/**
			 * SPECS
			 * ----------------------------------------------------------------------------------------------
			 */
			$this->addSubForm(new Ape_Form_SubForm(), 'spec', 20);
			$this->getSubForm('spec')->setLegend('estate_spec_data');

			$this->getSubForm('spec')->addElement('select', 'type', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_type',
				'class' => 'span12',
				'multiOptions' => $spec_fields->getField('type'),
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('select', 'province', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_province',
				'class' => 'span12',
				'multiOptions' => $spec_fields->getField('province'),
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'district', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_district',
				'class' => 'span12',
				'placeholder' => 'Brooklyn',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'address', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_address',
				'class' => 'span12',
				'placeholder' => '69 Est 17th Street ',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('select', 'category', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_category',
				'class' => 'span12',
				'multiOptions' => $spec_fields->getField('category'),
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('select', 'kind', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_kind',
				'class' => 'span12',
				'multiOptions' => $spec_fields->getField('kind'),
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'metrics', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_metrics',
				'class' => 'span12',
				'placeholder' => '120',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'rooms', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_rooms',
				'class' => 'span12',
				'placeholder' => '1',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'bathrooms', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_bathrooms',
				'class' => 'span12',
				'placeholder' => '2',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('checkbox', 'balcony', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_balcony',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('spec')->addElement('checkbox', 'elevator', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_elevator',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('spec')->addElement('checkbox', 'parking', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_parking',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('spec')->addElement('checkbox', 'garage', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_garage',
				'CheckedValue' => 'yes',
				'UncheckedValue' => 'no',
				'decorators' => $this->_small_checkbox_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'heating', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_heating',
				'class' => 'span12',
				'placeholder' => 'autonomo',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'floor', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_floor',
				'class' => 'span12',
				'placeholder' => '13',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'floors', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_floors',
				'class' => 'span12',
				'placeholder' => '14',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('select', 'state', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_state',
				'class' => 'span12',
				'multiOptions' => $spec_fields->getField('state'),
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'build_year', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_build_year',
				'placeholder' => '1968',
				'class' => 'span12',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'deed', array(
				'filters' => array('StringTrim'),
				'label' => 'estate_deed',
				'class' => 'span12',
				'placeholder' => 'libero/occupato/altro',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'monthly_charges', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_monthly_charges',
				'class' => 'span12',
				'placeholder' => '100',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addElement('text', 'price', array(
				'filters' => array('StringTrim', 'Digits'),
				'label' => 'estate_price',
				'class' => 'span12',
				'placeholder' => '1000',
				'decorators' => $this->_element_decorators
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'type',
					'province',
					'district',
					'address'
				), 'row-1', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 10
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'category',
					'kind',
					'metrics',
					'rooms'
				), 'row-2', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 20
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'heating',
					'floor',
					'floors',
					'state'
				), 'row-3', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 30
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'build_year',
					'deed',
					'monthly_charges',
					'price'
				), 'row-4', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 40
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'bathrooms',
					'balcony',
					'elevator',
					'parking',
					'garage'
				), 'row-5', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 50
			));

			$this->addElement('hidden', 'id', array(
				'required' => false,
				'decorators' => array('ViewHelper')
			));
		}
}