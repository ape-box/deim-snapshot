<?php

class page_forms_New extends Ape_Form
{
	public function init()
		{
			$this->addSubForm(new Ape_Form_SubForm(), 'base', 0);
			$this->getSubForm('base')->setLegend('Dati base');

			$this->getSubForm('base')->addElement('text', 'title', array(
				'filters' => array('StringTrim'),
				'required' => true,
				'class' => 'span12',
				'label' => 'Titolo',
				'placeholder' => 'Chi siamo',
				'order' => 10,
				'decorators' => $this->_large_element_decorators
			));

			$this->getSubForm('base')->addElement('textarea', 'text', array(
				'filters'	=> array('StringTrim'),
				'required'   => true,
				'label'	  => 'Contenuto',
				'class' => 'tiny span12',
				'cols' => 20,
				'rows' => 10,
				'order' => 20,
				'decorators' => $this->_textarea_decorators
			));

			$this->getSubForm('base')->addElement('button', 'submit', array(
				'required' => false,
				'ignore' => true,
				'type' => 'submit',
				'class' => 'btn btn-primary',
				'label' => 'salva la pagina',
				'decorators' => array(array('decorator' => 'ViewHelper', 'options' => array()))
			));

			$this->getSubForm('base')->addDisplayGroup(array('title'), 'row-1', array(
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

			$this->addSubForm(new Ape_Form_SubForm(), 'spec', 1);
			$this->getSubForm('spec')->setLegend('SEO Meta Tags');

			$this->getSubForm('spec')->addElement('textarea', 'keywords', array(
				'filters'	=> array('StringTrim'),
				'class' => 'span12',
				'label' => 'Keywords',
				'cols' => 10,
				'rows' => 10,
				'placeholder' => 'real estate, immobiliare facile, affitto, vendita, etc ...',
				'decorators' => array(
					array('decorator' => 'ViewHelper', 'options' => array()),
					'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
					array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
					array('decorator' => 'Errors', 'options' => array()),
					'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span4')),
				)
			));

			$this->getSubForm('spec')->addElement('textarea', 'description', array(
				'filters'	=> array('StringTrim'),
				'class' => 'span12',
				'label' => 'Description',
				'cols' => 20,
				'rows' => 10,
				'decorators' => array(
					array('decorator' => 'ViewHelper', 'options' => array()),
					'element' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'controls')),
					array('decorator' => 'Label', 'options' => array('requiredSuffix' => '*', 'class' => 'control-label')),
					array('decorator' => 'Errors', 'options' => array()),
					'row' => new Zend_Form_Decorator_HtmlTag(array('tag' => 'div', 'class' => 'control-group span8')),
				)
			));

			$this->getSubForm('spec')->addDisplayGroup(array(
					'keywords',
					'description'
				), 'row-1', array(
				'decorators' => array(
					'FormElements',
					array('HtmlTag', array('tag'=>'div','class'=>'row-fluid'))
				),
				'order' => 10
			));

			$this->addElement('hidden', 'id', array(
				'required'  => false,
				'decorators' => array('ViewHelper')
			));
		}
}